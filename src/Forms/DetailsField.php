<?php

namespace NSWDPC\Forms\DetailsField;

use Silverstripe\Forms\CompositeField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;
use SilverStripe\ORM\ValidationResult;

/**
 * DetailsField
 *
 * Provides a field that is rendered by default as a <details> element with a
 * <summary> and flow content
 * The flow content is made up of child fields you set on this CompositeField
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/details
 *
 * When the open event is triggered on the field, eg. someone clicks or hits enter
 * on the <summary>, the flow content is shown
 *
 * The field is triggered to be open by default when a validation message is present
 *
 * @author James
 */
class DetailsField extends CompositeField
{

    /**
     * Automatically add <strong> semantics around
     * string summary text
     * @var bool
     */
    private static $auto_strong = true;

    /**
     * @var bool
     */
    protected $isOpen = false;

    /**
     * Set summary content for the field, shown in a <summary> tag
     * Permitted content: Phrasing content or one element of Heading content
     * @param string|DBHTMLVarchar
     * @return self
     */
    public function setSummary($summary) : self
    {
        if (!($summary instanceof DBHTMLVarchar)) {
            $openTag = $closeTag = "";
            if ($this->config()->get('auto_strong')) {
                $openTag = "<strong>";
                $closeTag = "</strong>";
            }
            $summary = DBField::create_field(
                DBHTMLVarchar::class,
                $openTag . htmlspecialchars($summary) . $closeTag
            );
        }
        $this->title = $summary;
        return $this;
    }

    /**
     * Return summary content for the field, shown in a <summary> tag
     * @return string|DBHTMLVarchar|null
     */
    public function Summary()
    {
        return $this->summary;
    }

    /**
     * The title is the summary, this is implemented for consistency
     * @param string|DBHTMLVarchar
     * @return self
     */
    public function setTitle($title)
    {
        return $this->setSummary($title);
    }

    /**
     * Whenever the field has a message, the field is open by default
     * @inheritdoc
     */
    public function setMessage(
        $message,
        $messageType = ValidationResult::TYPE_ERROR,
        $messageCast = ValidationResult::CAST_TEXT
    ) {
        if($message !== "") {
            $this->setIsOpen(true);
        }
        return parent::setMessage($message, $messageType, $messageCast);
    }

    /**
     * Set the open state of the <details> element, it can be open or not
     * @param bool
     * @return self
     */
    public function setIsOpen(bool $is) : self
    {
        $this->isOpen = $is;
        return $this;
    }

    /**
     * Return the open state of the <details> element, it can be open or not
     * If a child field has a field message, then this field is triggered open by default
     * Provided open_when_child_message=true (the default)
     * @return bool
     */
    public function IsOpen() : bool
    {
        if($this->config()->get('open_when_child_message')) {
            $childFields = $this->FieldList();
            foreach($childFields as $field) {
                if($field->getMessage() !== "") {
                    $this->setIsOpen(true);
                    break;
                }
            }
        }
        return $this->isOpen;
    }
}
