# Details field for Silverstripe

This module implements a FormField for Silverstripe using the [HTML Details element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/details).

The field is a CompositeField allowing zero or more child fields to be displayed when the element is in an open state.

It's useful for containing long selection fields and/or optional information not required in a form submission.

## Usage

After module installation, use the DetailsField in code:

### Adding child fields

```php
<?php
namespace MyApp;

use NSWDPC\Forms\DetailsField\DetailsField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;

// ... etc

$childFields = FieldList::create(
    TextField::create('Salutation', _t('myapp.SALUTATION','Salutation')),
    TextField::create('FirstName', _t('myapp.FIRST_NAME','First name')),
    TextField::create('Surname', _t('myapp.SURNAME','Surname'))
);

$detailsField = DetailsField::create($childFields)
    ->setTitle(
        _t(
            'myapp.PROVIDE_DETAILS',
            'Provide some optional information'
        )
    )->setDescription(
        _t(
            'myapp.PROVIDE_DETAILS_DESCRIPTION',
            'A field description'
        )
    );
    
// by default the element is in a closed state, to open by default (or when values are present in the child fields)
// $detailsField = $detailsField->setIsOpen(true)

// push onto form fields
$fields->push($detailsField);
// ... etc

```

See tests/DetailsFieldTest for more.

## Example rendered field

<details>
    <summary><strong>Provide some optional information</strong>
    <p class="description">A field description</p>
    </summary>
    <div><label><strong>Salutation</strong> [___________]</label></div>
    <div><label><strong>First name</strong> [___________]</label></div>
    <div><label><strong>Surname</strong>    [___________]</label></div>
</details>

## Extras

+ `setTitle` is aliased to the `setSummary` method, both set the `title` property value on the field
+ You can set a standard `FormField` description, right title and field validation message
+ `IsOpen` and `Summary` return those property values, for use in templates
+ As the `<summary>` element can contain certain HTML, you can provide a DBHTMLVarchar field as the value to setTitle or setSummary
+ When the field or a child field has a validation message, the details element will be open by default

## Installation

```shell
composer require nswdpc/silverstripe-details-field
```

## License

[BSD-3-Clause](./LICENSE.md)

## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
