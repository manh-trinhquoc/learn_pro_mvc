<?php
namespace Framework;

use Framework\Base as Base;
use Framework\ArrayMethods as ArrayMethods;
use Framework\StringMethods as StringMethods;
use Framework\Template\Exception as Exception;

class Template extends Base
{
    /**
     * @readwrite
     */
    protected $_implementation;

    /**
     * @readwrite
     */
    protected $_header = "if (is_array(\$_data) && sizeof(\$_data))
        extract(\$_data); \$_text = array();";

    /**
     * @readwrite
    */
    protected $_footer = "return implode(\$_text);";
    
    /**
     * @read
    */
    protected $_code;

    /**
     * @read
     */
    protected $_function;
    
    public function _getExceptionForImplementation($method)
    {
        return new Exception\Implementation("{$method} method not implemented");
    }

    protected function _arguments($source, $expression)
    {
        $args = $this->_array($expression, array(
            $expression => array(
                "opener" => "{",
                "closer" => "}"
            )
        ));
        $tags=$args["tags"];
        $arguments =array();
        $sanitized =StringMethods::sanitize($expression, "()[],.<>*$@");
        foreach ($tags as $i => $tag) {
            $sanitized = str_replace($tag, "(.*)", $sanitized);
            $tags[$i] = str_replace(array("{", "}"), "", $tag);
        }
        if (preg_match("#{$sanitized}#", $source, $matches)) {
            foreach ($tags as $i => $tag) {
                $arguments[$tag]= $matches[$i + 1];
            }
        }
        return $arguments;
    }
    protected function _tag($source)
    {
        $tag= null;
        $arguments = array();
        $match = $this->_implementation->match($source);
        if ($match == null) {
            return false;
        }
        $delimiter=$match["delimiter"];
        $type =$match["type"];
        $start =strlen($type["opener"]);
        $end =strpos($source, $type["closer"]);
        $extract=substr($source, $start, $end - $start);
        if (isset($type["tags"])) {
            $tags =implode("|", array_keys($type["tags"]));
            $regex ="#^(/){0,1}({$tags})\s*(.*)$#";
            if (!preg_match($regex, $extract, $matches)) {
                return false;
            }
            $tag =$matches[2];
            $extract=$matches[3];
            $closer=!!$matches[1];
        }
        if ($tag && $closer) {
            return array(
                "tag" => $tag,
                "delimiter" =>$delimiter,
                "closer" => true,
                "source" => false,
                "arguments" =>false,
                "isolated" =>$type["tags"][$tag]["isolated"]
            );
        }
        if (isset($type["arguments"])) {
            $arguments = $this->_arguments($extract, $type["arguments"]);
        } elseif ($tag && isset($type["tags"][$tag]["arguments"])) {
            $arguments = $this->_arguments($extract, $type["tags"][$tag]["arguments"]);
        }
        return array(
            "tag" =>  $tag,
            "delimiter" =>  $delimiter,
            "closer" =>  false,
            "source" =>  $extract,
            "arguments" => $arguments,
            "isolated" => (!empty($type["tags"]) ? $type["tags"][$tag]["isolated"]: false)
        );
    }

    /**
     * The _array() method essentially deconstructs a template string into arrays of tags, text, and a combination of the two
     *
     * @param string $source
     * @return array
     */
    protected function _array(string $source): array
    {
        $parts=array();
        $tags =array();
        $all = array();
        $type = null;


        while ($source) {
            $match = $this->_implementation->match($source);
            // var_dump($this->_implementation);
            // var_dump($source);
            // var_dump($match);
            $type = $match["type"];
            $opener = strpos($source, $type["opener"]);
            $closer = strpos($source, $type["closer"]) +strlen($type["closer"]);
            // var_dump($opener);
            // var_dump($closer);
            if ($opener !== false) {
                $parts[] = substr($source, 0, $opener);
                $tags[] = substr($source, $opener, $closer - $opener);
                $source = substr($source, $closer);
                // var_dump($parts);
                // var_dump($tags);
            } else {
                $parts[] = $source;
                $source = "";
            }
        }

        // var_dump($parts);
        foreach ($parts as $i => $part) {
            $all[] = $part;
            // var_dump($all);
            // var_dump($i);
            // var_dump($tags);
            if (isset($tags[$i])) {
                $all[] =$tags[$i];
            }
        }

        // var_dump($all);
        $text = ArrayMethods::clean($parts);
        // var_dump($text);
        $tags =  ArrayMethods::clean($tags);
        // var_dump($tags);
        $all =  ArrayMethods::clean($all);
        // var_dump($all);
        return array(
            "text" => $text,
            "tags" => $tags,
            "all" => $all
        );
    }

    /**
     * The _tree() method loops through the array of template segments, generated by the _array() method, and organizes them into a hierarchical structure
     *
     * @param array $array
     * @return array
     */
    protected function _tree(array $array): array
    {
        $root = array(
            "children" => array()
        );
        $current = &$root;
        // $root = 'test reference $current';
        var_dump($current);
        foreach ($array as $i => $node) {
            // var_dump($node);
            $result = $this->_tag($node);
            // var_dump($result);
            if ($result) {
                $tag = isset($result["tag"]) ? $result["tag"] : "";
                $arguments = isset($result["arguments"]) ? $result["arguments"] : "";
                if ($tag) {
                    if (!$result["closer"]) {
                        $last=ArrayMethods::last($current["children"]);
                        if ($result["isolated"] && is_string($last)) {
                            array_pop($current["children"]);
                        }
                        $current["children"][]=array(
                            "index" => $i,
                            "parent" => &$current,
                            "children" => array(),
                            "raw" => $result["source"],
                            "tag" => $tag,
                            "arguments" => $arguments,
                            "delimiter" => $result["delimiter"],
                            "number" => sizeof($current["children"])
                        );
                        $current=& $current["children"][sizeof($current["children"]) - 1];
                    } elseif (isset($current["tag"]) && $result["tag"] == $current["tag"]) {
                        $start=$current["index"]+1;
                        $length=$i - $start;
                        $current["source"]=implode(array_slice($array, $start,$length));
                        $current=& $current["parent"];
                    }
                } else {
                    $current["children"][]=array(
                        "index" => $i,
                        "parent" => &$current,
                        "children" => array(),
                        "raw" => $result["source"],
                        "tag" => $tag,
                        "arguments" => $arguments,
                        "delimiter" => $result["delimiter"],
                        "number" => sizeof($current["children"])
                    );
                }
            } else {
                $current["children"][]=$node;
            }
        }

        return $root;
    }
    protected function _script($tree)
    {
        $content=array();
        if (is_string($tree)) {
            $tree =addslashes($tree);
                return "\$_text[]=\"{$tree}\";";
        }
        if (sizeof($tree["children"])>0) {
            foreach ($tree["children"] as $child) {
                $content[] =$this->_script($child);
            }
        }
        if (isset($tree["parent"])) {
            return $this->_implementation->handle($tree, implode($content));
        }
        return implode($content);
    }
    public function parse($template)
    {
        if (!is_a($this-> _implementation, "Framework\Template\Implementation")) {
            throw new Exception\Implementation('property implementation is not an instance of valid class');
        }

        $array = $this->_array($template);
        // var_dump($array);
        $tree = $this->_tree($array["all"]);
        // var_dump($tree);
        $this->_code = $this->header.$this->_script($tree).$this->footer;
        // var_dump($this->header);
        // var_dump($this->_script($tree));
        // var_dump($this->footer);
        // var_dump($this->_code);
        $this->_function = create_function("\$_data", $this->code);
        //TODO: replace create_function with other solution
        return $this;
    }

    public function process($data=array())
    {
        if ($this->_function == null) {
            throw new Exception\Parser();
        } try {
            $function = $this->_function;
            return $function($data);
        } catch (\Exception $e) {
            throw new Exception\Parser($e);
        }
    }
}