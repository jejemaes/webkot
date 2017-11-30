<?php
/**
 * Maes Jerome
 * IrQweb.class.php, created at May 4, 2015
 *
 */
namespace system\lib\qweb;

use \system\lib\qweb\QWebLoader as QWebLoader;
use \system\lib\qweb\QWebContext as QWebContext;
use \system\lib\qweb\QWebTemplateNotFound as QWebTemplateNotFound;
use \DOMDocument as DOMDocument;

function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}


class QWebEngine implements \system\interfaces\iTemplateEngine{
	
	protected static $_instance;
	
	
	public static function getEngine($loader){
		if (!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c($loader);
			self::$_instance->__construct($loader);
		}
		return self::$_instance;
	}
	

	private $_loader;
	private $_patterns;

	/**
	 * Instanciate the template engine
	 * @param iTemplateLoader $loader : the default template loader to use during the rendering phase.
	 */
	public function __construct($loader){
		$this->_loader = $loader;
		$this->_patterns = array(
				'/(?:#\{(.+?)\})/', 	// ruby-style pattern
				'/(?:\{\{(.+?)\}\})/' // jinja-style pattern
				);

		$this->_methods = array();
		foreach(get_class_methods($this) as $m){
			if(startsWith($m, 'render_att_') || startsWith($m, 'render_tag_')){
				$this->_methods[] = $m;
			}
		}
	}

	/**
	 * Add a parsed template in the context. Used to preprocess templates.
	 * @param unknown $qwebcontext
	 * @param unknown $name
	 * @param unknown $node
	 */
	function add_template($qwebcontext, $name, $node){
		$qwebcontext['template'][$name] = $node;
	}

	/**
	 * Loads an XML document and installs any contained template in the engine
	 * type document: a parsed lxml.etree element, an unparsed XML document
	 *                  (as a string) or the path of an XML file to load
	 * @param unknown $document
	 * @param unknown $res_id
	 * @param unknown $qwebcontext
	 */
	function load_document($document, $qwebcontext){
		$dom = $document;
		if(is_string($document)){
			$dom = new DOMDocument();
			$dom->loadXML($document);
		}
		$root = $dom->documentElement;
		$children = $dom->getElementsByTagName('t');
		foreach($children as $node){
			if($node->hasAttribute("t-name")){
				$this->add_template($qwebcontext, $node->getAttribute('t-name'), $node);
			}
		}
	}

	/**
	 * Tries to fetch the template ``name``, either gets it from the
	 *  context's template cache or loads one with the context's loader (if
	 *  any).
	 *  :raises QWebTemplateNotFound: if the template can not be found or loaded
	 * @param unknown $name
	 * @param unknown $qwebcontext
	 */
	function get_template($name, $qwebcontext){
		$origin_template = false;
		if($qwebcontext['__caller__']){
			$origin_template = $qwebcontext['__caller__'];
		}else{
			$origin_template = $qwebcontext['__stack__'][0];
		}
		// load the template of it is not in the qwebcontext
		if($this->_loader && !isset($qwebcontext['template'][$name])){
			$loader = $this->_loader;
			$xml_doc = $loader($name);
			$this->load_document($xml_doc, $qwebcontext);
		}
		if(array_key_exists($name, $qwebcontext['template'])){
			return $qwebcontext['template'][$name];
		}
		// TODO raise exception : template not found by loader
		throw new QWebTemplateNotFound(sprintf("Template %s not found", $name));
	}


	function find_method_match($prefix, $short_name){
		foreach ($this->_methods as $method_name) {
			$m = $prefix . str_replace('-', '_', $short_name);
			if(startsWith($m, $method_name)){
				return $method_name;
			}
		}
		return FALSE;
	}

	// EVAL FUNCTIONS

	function _eval($expr, $qwebcontext, $default=FALSE){
		try {
			extract($qwebcontext['data']);
			return eval("return $expr;");
		} catch (\ErrorException $e) {
			// Do nothing. This catches case where variable is not defined, and should be 
			// interpreted as False, or empty string, ... like in python
		}
		return $default;
	}

	function eval_object($expr, $qwebcontext){
		return $this->_eval($expr, $qwebcontext, NULL);
	}

	function eval_str($expr, $qwebcontext){
		// string("0")
		if($expr == "0"){
			return $qwebcontext[0] ?: "";
		}
		$val = $this->_eval($expr, $qwebcontext);
		if(is_string($val)){
			return $val;
		}
		// bool(false) or null
		if(!$val or $val == null){
			return "";
		}
		return $val;
	}

	function eval_bool($expr, $qwebcontext){
		return (bool) $this->_eval($expr, $qwebcontext);
	}

	function eval_format($expr, $qwebcontext){

		/*
		 foreach ($this->_patterns as $regex) {
		if (preg_match_all($regex, $expr, $matches_out)) {
		echo "IN <br>";
		print_r($matches_out[0]);
		}
		}
		*/

		$this->qwebcontext2 = clone $qwebcontext;
		$qwebcontext2 = clone $qwebcontext;

		global $self;
		$self = &$this;
		//$replacements = 0; // number of replacements

		// TODO : use closure ; myabe they can pas other arg, create a function with a function, ... to properly pass qwebcontext !

		// apply pattern, find them, and replace them with eval_str(string_found)
		$expr_replaced = preg_replace_callback(
			$this->_patterns,
			function ($matches, $qwebcontext) {
					// match 1 is the content between {}
				return $this->eval_str($matches[1], $qwebcontext);
			},
			$expr,
			-1,
			$replacements);

		if($replacements){
			return $expr_replaced;
		}
		return $expr;
		// TODO
		/*
		try:
		return str(expr % qwebcontext) ---> sprintf or preg_replace ??
		except Exception:
		template = qwebcontext.get('__template__')
		raise_qweb_exception(message="Format error for expression %r" % expr, expression=expr, template=template)
		*/
	}


	// RENDER METHODS

	/**
	 * (non-PHPdoc)
	 * @see \system\interfaces\iTemplateEngine::render()
	 */
	public function render($id_or_xml_id, $qwebcontext, $loader=NULL){
		if($loader){
			$this->_loader = $loader;
		}
		
		if(is_array($qwebcontext)){
			$qwebcontext = new QWebContext($qwebcontext);
		}
		
		$qwebcontext['__template__'] = $id_or_xml_id;
		$stack = $qwebcontext['__stack__'] ?: array();
		if(count($stack)){
			$qwebcontext['__caller__'] = end($stack);
		}
		array_push($stack, $id_or_xml_id);
		$qwebcontext['__stack__'] = $stack;
		return $this->render_node($this->get_template($id_or_xml_id, $qwebcontext), $qwebcontext);
	}


	function render_node($element, $qwebcontext){
		// if Only text node, return it immediately
		if(get_class($element) == 'DOMText'){
			return $element->textContent;
		}

		$generated_attributes = "";
		$t_render = false;
		$result = false;
		$template_attributes = array();

		if($element->attributes){
			foreach($element->attributes as $attribute_name => $attribute_node){
				$attribute_value = $attribute_node->nodeValue;
				$attribute_short_name = substr($attribute_name, 2);
				// groups attribute
				if($attribute_name == 'groups'){
					die("GROUPS is not implemented yet !");
				}
				// qweb attributes
				if(startsWith($attribute_name, 't-')){
					$render_att_method = $this->find_method_match('render_att_', $attribute_short_name);
					if(method_exists($this, $render_att_method)){
						$attrs = $this->$render_att_method($element, $attribute_name, $attribute_value, $qwebcontext);
						foreach ($attrs as $att => $value){
							if(!$value){
								continue;
							}
							$generated_attributes .= $this->render_attribute($element, $att, $value, $qwebcontext);
						}
					}else{
						$render_att_tag = $this->find_method_match('render_tag_', $attribute_short_name);
						if(method_exists($this, $render_att_tag)){
							$t_render = $attribute_short_name;
						}
						$template_attributes[$attribute_short_name] = $attribute_value;
					}
				}else{
					$generated_attributes .= $this->render_attribute($element, $attribute_name, $attribute_value, $qwebcontext);
				}
			}
		}

		if($t_render){
			$method = $this->find_method_match('render_tag_', $t_render);
			$result = $this->$method($element, $template_attributes, $generated_attributes, $qwebcontext);
		}else{
			$result = $this->render_element($element, $template_attributes, $generated_attributes, $qwebcontext);
		}
		return $result;
	}

	/**
	* element: element
	* template_attributes: t-* attributes
	* generated_attributes: generated attributes
	* qwebcontext: values
	* inner: optional innerXml
	*/
	function render_element($element, $template_attributes, $generated_attributes, $qwebcontext, $inner=false){
		$g_inner = false;
		if($inner){
			$g_inner = $inner; //strlen($inner) != strlen(utf8_decode($inner)) ? utf8_encode($inner) : $inner;
			$g_inner = array($g_inner);
		}else{
			$g_inner = $element->textContent ? array() : array(utf8_encode($element->textContent));
			if($element->childNodes){
				foreach($element->childNodes as $node){
					try{
						array_push($g_inner, $this->render_node($node, $qwebcontext));
					}catch(QWebException $e){
						echo $e;
					}catch(Exception $e){
						throw new QWebException(sprintf("Could not render element %s : %s %s", $element->tagName, $element->nodeValue, $element->nodeType));
					}
				}
			}
		}
		$name = isset($element->tagName) ? $element->tagName : FALSE ;
		$inner = join("", $g_inner);
		$trim = array_key_exists('trim', $template_attributes) ? $template_attributes['trim'] : false;
		if($trim == 'left'){
			$inner = ltrim($inner);
		}else{
			if($trim == 'right'){
				$inner = rtrim($inner);
			}else{
				if($trim == 'both'){
					$inner = trim($inner);
				}
			}
		}
		if($name){
			if($name == "t"){
				return $inner;
			}
			if($inner){
				return sprintf("<%s%s>%s</%s>", $name, $generated_attributes, $inner, $name);
			}
			if(in_array($name, array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'menuitem', 'meta', 'param', 'source', 'track', 'wbr'))){
				return sprintf("<%s%s/>", $name, $generated_attributes);
			}
			return sprintf("<%s%s></%s>", $name, $generated_attributes, $name);
		}
		return '';	
	}


	function render_attribute($element, $name, $value, $qwebcontext){
		return sprintf(' %s="%s"', $name, htmlspecialchars($value));
	}



	// Attributes
	function render_att_att($element, $attribute_name, $attribute_value, $qwebcontext){
		if(startswith($attribute_name, 't-attf-')){
			return array(substr($attribute_name, 7) => $this->eval_format($attribute_value, $qwebcontext));
		}
		if(startswith($attribute_name, 't-att-')){
			return array(substr($attribute_name, 6) => $this->_eval($attribute_value, $qwebcontext));
		}
		$result = $this->eval_object($attribute_value, $qwebcontext);
		if(is_array($result)){
			return $result; // TODO not sure :  return result.iteritems()
		}
		return array($result);
	}


	// Tags
	function render_tag_raw($element, $template_attributes, $generated_attributes, $qwebcontext){
		$inner = $this->eval_str($template_attributes["raw"], $qwebcontext);
		return $this->render_element($element, $template_attributes, $generated_attributes, $qwebcontext, $inner);
	}

	function render_tag_esc($element, $template_attributes, $generated_attributes, $qwebcontext){
		/* TODO widget ?
		$options = json_decode($template_attributes['esc-options'] ?: array());
		$widget = self.get_widget_for(options.get('widget'));

		widget = self.get_widget_for(options.get('widget'))
		inner = widget.format(template_attributes['esc'], options, qwebcontext)
		*/

		$inner = htmlspecialchars($this->eval_str($template_attributes['esc'], $qwebcontext));
		return $this->render_element($element, $template_attributes, $generated_attributes, $qwebcontext, $inner);
	}

	function render_tag_foreach($element, $template_attributes, $generated_attributes, $qwebcontext){
		$expr = $template_attributes["foreach"];
		$enum = $this->eval_object($expr, $qwebcontext);
		
		if(!is_array($enum)){
			$template = $qwebcontext['__template__'];
			throw new QWebException(sprintf("foreach enumerator %s is not defined while rendering template %s", $expr, $template));
		}
		if(is_int($enum)){
			$enum = range(0, $enum);
		}

		$varname = str_replace(".", "_", $template_attributes['as']);
		$varname = str_replace("$", "", $varname);

		$copy_qwebcontext = clone $qwebcontext;
		$size = NULL;
		if(is_array($enum)){
			$size = count($enum);
			$copy_qwebcontext['data'][sprintf("%s_size", $varname)] = $size;
		}

		$copy_qwebcontext['data'][sprintf("%s_all", $varname)] = $enum;
		$ru = array();

		$index = 0;
		foreach( $enum as $key => $value ){
			// update dict values
			$copy_qwebcontext['data'][$varname] = is_int($key) ? $value : $key;
			$copy_qwebcontext['data'][sprintf('%s_value', $varname)] = $value;
			$copy_qwebcontext['data'][sprintf('%s_index', $varname)] = $index;
			$copy_qwebcontext['data'][sprintf('%s_first', $varname)] = (bool)($index == 0);

			if($size != NULL){
				$copy_qwebcontext['data'][sprintf('%s_last', $varname)] = (bool)($index + 1 == $size);
			}

			if($index % 2){
				$copy_qwebcontext['data'][sprintf('%s_parity', $varname)] = 'odd';
				$copy_qwebcontext['data'][sprintf('%s_odd', $varname)] = True;
				$copy_qwebcontext['data'][sprintf('%s_even', $varname)] = False;
			}else{
				$copy_qwebcontext['data'][sprintf('%s_parity', $varname)] = 'even';
				$copy_qwebcontext['data'][sprintf('%s_odd', $varname)] = False;
				$copy_qwebcontext['data'][sprintf('%s_even', $varname)] = True;
			}
			$ru[] = $this->render_element($element, $template_attributes, $generated_attributes, $copy_qwebcontext);
			$index += 1;
		}
		return join("", $ru);
	}

	function render_tag_if($element, $template_attributes, $generated_attributes, $qwebcontext){
		if($this->eval_bool($template_attributes["if"], $qwebcontext)){
			return $this->render_element($element, $template_attributes, $generated_attributes, $qwebcontext);
		}
		return "";
	}

	function render_tag_call($element, $template_attributes, $generated_attributes, $qwebcontext){
		$d = clone $qwebcontext;
		$d[0] = $this->render_element($element, $template_attributes, $generated_attributes, $d);
		$template = $this->eval_format($template_attributes["call"], $d);
		return $this->render($template, $d);
	}

	function render_tag_set($element, $template_attributes, $generated_attributes, $qwebcontext){
		$set_varname = str_replace("$", "", $template_attributes["set"]);
		if(array_key_exists("value", $template_attributes)){
			$qwebcontext['data'][$set_varname] = $this->eval_object($template_attributes["value"], $qwebcontext);
		}elseif(array_key_exists("valuef", $template_attributes)){
			$qwebcontext['data'][$set_varname] = $this->eval_format($template_attributes["valuef"], $qwebcontext);
		}else{
			$qwebcontext['data'][$set_varname] = $this->render_element($element, $template_attributes, $generated_attributes, $qwebcontext);
		}
		return "";
	}

}