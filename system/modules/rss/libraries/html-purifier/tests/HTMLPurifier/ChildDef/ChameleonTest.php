<?php

require_once 'HTMLPurifier/ChildDefHarness.php';
require_once 'HTMLPurifier/ChildDef/Chameleon.php';

class HTMLPurifier_ChildDef_ChameleonTest extends HTMLPurifier_ChildDefHarness
{
    
    var $isInline;
    
    function setUp() {
        parent::setUp();
        $this->obj = new HTMLPurifier_ChildDef_Chameleon(
            'b | i',      // allowed only when in inline context
            'b | i | div' // allowed only when in block context
        );
        $this->context->register('IsInline', $this->isInline);
    }
    
    function testInlineAlwaysAllowed() {
        $this->isInline = true;
        $this->assertResult(
            '<b>Allowed.</b>'
        );
    }
    
    function testBlockNotAllowedInInline() {
        $this->isInline = true;
        $this->assertResult(
            '<div>Not allowed.</div>', ''
        );
    }
    
    function testBlockAllowedInNonInline() {
        $this->isInline = false;
        $this->assertResult(
            '<div>Allowed.</div>'
        );
    }
    
}

