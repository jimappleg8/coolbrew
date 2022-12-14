<?php

require_once 'HTMLPurifier/Lexer/DirectLex.php';

/**
 * General-purpose test-harness that makes testing functions that require
 * configuration and context objects easier when those two parameters are
 * meaningless.  See HTMLPurifier_ChildDefTest for a good example of usage.
 */
class HTMLPurifier_ComplexHarness extends HTMLPurifier_Harness
{
    
    /**
     * Instance of the object that will execute the method
     */
    var $obj;
    
    /**
     * Name of the function to be executed
     */
    var $func;
    
    /**
     * Whether or not the method deals in tokens. If set to true, assertResult()
     * will transparently convert HTML to and back from tokens.
     */
    var $to_tokens = false;
    
    /**
     * Whether or not to convert tokens back into HTML before performing
     * equality check, has no effect on bools.
     */
    var $to_html = false;
    
    /**
     * Instance of an HTMLPurifier_Lexer implementation.
     */
    var $lexer;
    
    /**
     * Instance of HTMLPurifier_Generator
     */
    var $generator;
    
    /**
     * Default config to fall back on if no config is available
     */
    var $config;
    
    /**
     * Default context to fall back on if no context is available
     */
    var $context;
    
    function HTMLPurifier_ComplexHarness() {
        $this->lexer     = new HTMLPurifier_Lexer_DirectLex();
        $this->generator = new HTMLPurifier_Generator();
        parent::HTMLPurifier_Harness();
    }
    
    /**
     * Asserts a specific result from a one parameter + config/context function
     * @param $input Input parameter
     * @param $expect Expectation
     * @param $config Configuration array in form of Ns.Directive => Value.
     *                Has no effect if $this->config is set.
     * @param $context_array Context array in form of Key => Value or an actual
     *                       context object.
     */
    function assertResult($input, $expect = true) {
        
        if ($this->to_tokens && is_string($input)) {
            // $func may cause $input to change, so "clone" another copy
            // to sacrifice
            $input   = $this->tokenize($temp = $input);
            $input_c = $this->tokenize($temp);
        } else {
            $input_c = $input;
        }
        
        // call the function
        $func = $this->func;
        $result = $this->obj->$func($input_c, $this->config, $this->context);
        
        // test a bool result
        if (is_bool($result)) {
            $this->assertIdentical($expect, $result);
            return;
        } elseif (is_bool($expect)) {
            $expect = $input;
        }
        
        if ($this->to_html) {
            $result = $this->generate($result);
            if (is_array($expect)) {
                $expect = $this->generate($expect);
            }
        }
        
        $this->assertIdentical($expect, $result);
        
    }
    
    /**
     * Tokenize HTML into tokens, uses member variables for common variables
     */
    function tokenize($html) {
        return $this->lexer->tokenizeHTML($html, $this->config, $this->context);
    }
    
    /**
     * Generate textual HTML from tokens
     */
    function generate($tokens) {
        return $this->generator->generateFromTokens($tokens, $this->config, $this->context);
    }
    
}


