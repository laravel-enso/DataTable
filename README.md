<!--h-->
# Data Table
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ee67de45d1f14dbd98eb72c8cf972902)](https://www.codacy.com/app/laravel-enso/DataTable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/DataTable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85495802/shield?branch=master)](https://styleci.io/repos/85495802)
[![License](https://poser.pugx.org/laravel-enso/datatable/license)](https://https://packagist.org/packages/laravel-enso/datatable)
[![Total Downloads](https://poser.pugx.org/laravel-enso/datatable/downloads)](https://packagist.org/packages/laravel-enso/datatable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/datatable/version)](https://packagist.org/packages/laravel-enso/datatable)
<!--/h-->

DataTable package for the [DataTables.net](https://datatables.net/) library with server-side processing and a VueJS component.

[![Watch the demo](https://laravel-enso.github.io/datatable/screenshots/bulma_001_thumb.png)](https://laravel-enso.github.io/datatable/videos/bulma_demo_01.webm)
<sup>click on the photo to view a short demo in compatible browsers</sup>

### Details
Supports:
- server side data loading, with multi-argument
- searching in all the columns of the table (you can also choose to exclude columns)
- customizable column visibility
- beautiful tag rendering for boolean flag columns
- striped rows for a pleasant aspect
- supports custom rendering of data in columns
- state save for each table, for certain options and preferences, with the option to reset everything to default
- reordering of columns
- sorting on any column (you can also choose to exclude some columns within the configuration)
- user configurable pagination
- reloading of data on demand
- automatic display of show/edit/delete buttons based on available permissions
- server-side excel exporting of the table data, using current search filtering, up to a configurable limit
- permits overriding of the appends attribute for the main query model
- sublime snippet for table template
and more

### Installation

The component is already included in the Enso install and should not require any additional installation steps.

### Use

1. Optionally, you may publish the example table structure class
    ```
    php artisan vendor:publish --tag=datatable-class
    ```    

2. In your controller add the `DataTable` trait. This includes two helper methods that will manage the builder:
	- initTable
	- getTableData
	
3. In the controller you must define a method for the query builder, such as:
    ```
    public function getTableQuery()
    {
        return MyModel::select(\DB::raw('id as DT_RowId, attribute1, ..., attributeN'));
    }
    ```

    Note that it should return a QueryBuilder object and not a collection of results.

4. Also in the controller add `protected $tableStructureClass = MyTableStructure::class` which should be the fully qualified class name describing the structure of the table rendered in your page

5. In your routes files add the routes for the helper methods, and name them `myRoute.initTable`, `myRoute.getTableData` and, optionally, `myRoute.exportExcel` if you want the results exporting functionality

6. If you need to, don't forget to add user permissions for the new routes

7. In your page/component add:
    ```
    <data-table
        source="myRoute"
        id="my-table-id"
        :custom-render="customRender">
    </data-table>
    ```

8. Configure the table from the structure class

### Options

- `source` - required, must reference the controllers base route, where both initTable & getTableData endpoints exist
- `extra-filters` - reactive object of the following format
    ```
    "extraFilters": {
        "table": {
            "field_1" : '',
            "field_2" : '',
        }
    }
    ```
- `custom-params` - extra parameters sent to the back-end for custom logic / queries
    ```
    "customParams": {
        "orders": {
            dispatched: ''
        }
    }
    ```
- `interval-filters` - where `dbDateFormat` is REQUIRED if the filter values are dates. The given format has to match the database date format
    ```
    "intervalFilters": {
       "table":{
          "created_at": {
             "min":"value",
             "max":"value",
             "dbDateFormat": "Y-m-d"
          },
          "amount": {
            "min": 0,
            "max": 1000
          }
       }
    }
    ```

### TableStructure
  - `crtNo` - the label for the current number column
  - `actionButtons` - array of types of custom buttons to render
  - `headerButtons` - array of types of buttons to render above the table header
  - `responsePriority` - the priority of columns in responsive mode, i.e. when the content doesn't fit on the screen 
  - `headerAlign` & `bodyAlign` - type of alignment for the text in cells, eg. 'center'
  - `icon` - the icon to be used for the card containing the datatable
  - `notSearchable` - simple array w/ the column indexes that are **NOT** searchable using the component search
  - `notSortable` - simple array w/ the column indexes that are **NOT** sortable
  - `boolean` - array of column indexes that contain values that should be treated as boolean
  - `enumMappings` - KV array, where key is the column name, and value is the Enum class name used for translation. These enums contain the translations for the flag-type values in your table, which you want to be presented in a more human friendly way, i.e. `Shipped`/`Delivered` instead of 5 / 6.
  - `appends` - optional array of model attributes, which can be appended before returning the query results <sup>1</sup> <sup>2</sup>
  - `columns` - array of arrays. Each inner array contains:
     - `label` - table column header label
     - `data` - the alias of data in query result, eg. 'owner'
     - `name` - the table column used when searching, eg. 'owner.name'

<sup>1</sup> If you are appending attributes fetched through a relationship, you'll need to include the model's id attribute in the raw query
<sup>2</sup> When using appended attributes, since these do not actually exist in the model's DB table, you need to exclude them using the `notSearchable` option, or you'll get errors when searching

### Config

  - `excelRowLimit` - the maximum number of exported entries, when using the export function. You may need to adjust this depending on your server's RAM and PHP settings.

### Publishes

- `php artisan vendor:publish --tag=datatable-component` - the VueJS component file
- `php artisan vendor:publish --tag=datatable-options` - the json options file
- `php artisan vendor:publish --tag=datatable-lang` - the default lang files
- `php artisan vendor:publish --tag=datatable-class` - the abstract TableStructure class that must be extended when creating specific structures
- `php artisan vendor:publish --tag=enso-update` - a common alias for when wanting to update the VueJS component,
once a newer version is released.

### Notes

The [Laravel Enso](https://github.com/laravel-enso/Enso) package comes with this package included. There you'll find working examples for using the component

In the snippets folder you'll find a sublime snippet for quickly creating a stub table-structure class

Depends on:
 - [Core](https://github.com/laravel-enso/Core) for the user model 
 - [Helpers](https://github.com/laravel-enso/Helpers) for some generic classes
 - [VueComponents](https://github.com/laravel-enso/VueComponents) for the accompanying VueJS components 
 - [Laravel-Excel](https://github.com/Maatwebsite/Laravel-Excel) for working the excel files


<!--h-->
### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
<!--/h-->