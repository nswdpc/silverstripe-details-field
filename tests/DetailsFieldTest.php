<?php

namespace NSWDPC\Forms\DetailsField\Tests;

use NSWDPC\Forms\DetailsField\DetailsField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;
use SilverStripe\ORM\ValidationResult;

/**
 * Tests for the DetailsField
 *
 * @author James
 */
class DetailsFieldTest extends SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = false;

    public function testIsOpen()
    {
        $detailsField = DetailsField::create();
        $detailsField->setName("testIsOpen");
        $detailsField = $detailsField->setSummary('Summary');
        $detailsField->setIsOpen(true);

        $this->assertTrue($detailsField->IsOpen());

        $template = $detailsField->FieldHolder();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();
        $details = $doc->getElementsByTagName('details')[0];
        $this->assertTrue($details->hasAttribute('open'), "<details> has open attribute");
    }

    public function testIsNotOpen()
    {
        $detailsField = DetailsField::create();
        $detailsField->setName("testIsNotOpen");
        $detailsField = $detailsField->setSummary('Summary');
        $detailsField->setIsOpen(false);

        $this->assertFalse($detailsField->IsOpen());

        $template = $detailsField->FieldHolder();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();
        $details = $doc->getElementsByTagName('details')[0];
        $this->assertFalse($details->hasAttribute('open'), "<details> has no open attribute");
    }

    public function testSummary()
    {
        DetailsField::config()->set('auto_strong', true);

        $summaryText = 'Summary text';
        $detailsField = DetailsField::create();
        $detailsField->setName("testSummary");
        $detailsField = $detailsField->setSummary($summaryText);

        $template = $detailsField->FieldHolder();

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();
        $summary = $doc->getElementsByTagName('summary')[0];
        $strong = $summary->getElementsByTagName('strong')[0];

        $this->assertEquals($summaryText, $strong->textContent, "Has <strong> around summary text");
    }

    public function testSummaryHTML()
    {
        DetailsField::config()->set('auto_strong', true);

        $summaryText = "HEADING_4";
        $heading = "<h4>{$summaryText}</h4>";
        $detailsField = DetailsField::create();
        $detailsField->setName("testSummaryHTML");
        $detailsField = $detailsField->setSummary(
            DBField::create_field(
                DBHTMLVarchar::class,
                $heading
            )
        );

        $template = $detailsField->FieldHolder();

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();
        $summary = $doc->getElementsByTagName('summary')[0];
        $heading = $summary->getElementsByTagName('h4')[0];

        $this->assertEquals($summaryText, trim($heading->textContent), "Has <h4> around summary text");
    }



    public function testChildFields()
    {
        $childFields = FieldList::create(
            TextField::create('Salutation', _t('myapp.SALUTATION', 'Salutation')),
            TextField::create('FirstName', _t('myapp.FIRST_NAME', 'First name')),
            TextField::create('Surname', _t('myapp.SURNAME', 'Surname'))
        );

        $detailsField = DetailsField::create($childFields);
        $detailsField->setName("testChildFields");
        $detailsField = $detailsField->setSummary('CHILD_FIELDS');

        $template = $detailsField->FieldHolder();

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();
        $details = $doc->getElementsByTagName('details')[0]; // DOMNode
        $inputs = $details->getElementsByTagName('input');// DOMNodeList
        $summary = $details->getElementsByTagName('summary')[0]; // DOMNode
        $this->assertEquals('CHILD_FIELDS', trim($summary->textContent));
        $fieldlist = $detailsField->FieldList();
        $this->assertEquals($childFields->count(), $fieldlist->count(), "Matching field count");
        $this->assertEquals($childFields->count(), $inputs->count(), "Correct number of inputs");
    }

    public function testExtras() {

        $childFields = FieldList::create(
            TextField::create('Salutation', _t('myapp.SALUTATION', 'Salutation')),
            TextField::create('FirstName', _t('myapp.FIRST_NAME', 'First name')),
            TextField::create('Surname', _t('myapp.SURNAME', 'Surname'))
        );

        $detailsField = DetailsField::create($childFields);
        $detailsField->setName("testExtra");
        $detailsField = $detailsField->setSummary('CHILD_FIELDS');
        $detailsField->setDescription("DESCRIPTION");
        $detailsField->setRightTitle("RIGHT_TITLE");
        $detailsField->setMessage("MESSAGE", ValidationResult::TYPE_ERROR);

        $template = $detailsField->FieldHolder();

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();

        $details = $doc->getElementsByTagName('details')[0]; // DOMNode
        $this->assertTrue($details->hasAttribute('open'));// details is open when message
        $summary = $details->getElementsByTagName('summary')[0]; // DOMNode
        $extras = $summary->getElementsByTagName('p');

        $this->assertEquals( 3, $extras->count(), "All extras present");

        $expected = ["DESCRIPTION","RIGHT_TITLE","MESSAGE"];
        $found = [];
        foreach($extras as $extra) {
            $found[] = trim($extra->textContent);
        }

        $this->assertEmpty(array_diff($expected, $found), "All extras found");
    }

    public function testChildMessage() {

        $childFields = FieldList::create(
            TextField::create('Salutation', _t('myapp.SALUTATION', 'Salutation')),
            $firstNameField = TextField::create('FirstName', _t('myapp.FIRST_NAME', 'First name')),
            TextField::create('Surname', _t('myapp.SURNAME', 'Surname'))
        );

        $firstNameField->setMessage(
            "FIRSTNAME_MESSAGE",
            ValidationResult::TYPE_ERROR
        );

        $detailsField = DetailsField::create($childFields);
        $detailsField->setName("testChildMessage");
        $detailsField = $detailsField->setSummary('TEST_CHILD_FIELD_MESSAGE');
        $detailsField->setDescription("DESCRIPTION");
        $detailsField->setRightTitle("RIGHT_TITLE");

        $template = $detailsField->FieldHolder();

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($template);
        libxml_clear_errors();

        $details = $doc->getElementsByTagName('details')[0]; // DOMNode
        $this->assertTrue($details->hasAttribute('open'));// details is open when child field has a message
    }
}
