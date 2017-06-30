# Data Table
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ee67de45d1f14dbd98eb72c8cf972902)](https://www.codacy.com/app/laravel-enso/DataTable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/DataTable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85495802/shield?branch=master)](https://styleci.io/repos/85495802)
[![Total Downloads](https://poser.pugx.org/laravel-enso/datatable/downloads)](https://packagist.org/packages/laravel-enso/datatable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/datatable/version)](https://packagist.org/packages/laravel-enso/datatable)

DataTable package for the [DataTables.net](https://datatables.net/) library with server-side processing and a VueJS component.

[![Watch the demo](https://laravel-enso.github.io/datatable/screenshots/Selection_001.png)](https://laravel-enso.github.io/datatable/videos/demo_01.webm)
<sup>click on the photo to view short a demo</sup>  

### Details
Supports:
- searching in all the columns of the table (you can also choose to exclude some columns)
- hiding of columns
- reordering of columns
- sorting on any column (you can also choose to exclude some columns)
- totals
- user configurable pagination
- reloading of data
- visual aides, directly from the interface, such as displaying a table as compact and adding alternate row coloring
- inline editing of values<sup>1</sup> <sup>2</sup>
- custom rendering of data in columns
and more

<sup>1</sup> requires the purchase of the DataTables.net Editor library, as it's a commercial feature

<sup>2</sup> there are some limitations when editing aggregated data

### Installation

1. Add `LaravelEnso\DataTable\DataServiceProvider::class` to `config/app.php`.

2. Publish the vue component with `php artisan vendor:publish --tag=datatable-component`.

3. Publish the sample structure class with `php artisan vendor:publish --tag=datatable-structure`.

4. Include the vue component in your app.js.

5. Run `gulp` / `npm run dev`.

6. In your blade page add:

    ```
    <data-table source="/myRoute">
        <span slot="data-table-title">{{ __("Title") }}</span>
        <span slot="data-table-totals">{{ __("Totals") }}</span>
        @include('laravel-enso/core::partials.modal')
    </data-table>
    ```

7. In your controller add `use DataTable` to include the trait. This adds to your controller two helper methods that will manage the builder:
	- initTable
	- getTableData

8. In the controller you must define a method for the query builder, such as:

    ```
    public function getTableQuery()
    {
        return MyModel::select(\DB::raw('id as DT_RowId, attribute1, ..., attributeN'));
    }
    ```

9. Also in the controller add `protected $tableStructureClass = MyTableStructure::class` which should be the class describing the structure of the table rendered in your page 

10. In your routes files add two routes for the helper methods, and name them `myRoute.initTable` and `myRoute.getTableData`.

11. Configure the table from the structure class.

### Notes

- You may clone and/or install the [Laravel Enso](https://github.com/laravel-enso/Enso) package where you'll find working examples for using the component
- In the snippets folder you'll find a sublime snippet for quickly creating a stub table-structure class

### Options

	`source` - required, must reference the controllers base route, where both initTable & getTableData endpoints exist
	`header-class` - header class for the box element: info (default option) / default / primary / warning / danger / default
	`extra-filters` - reactive option array of the following format:
		extraFilters: {
	        "table": {
	            "field_1" : '',
	            "field_2" : '',
	        }
	    }
    `params` -
	    customParams: {
            orders: {
                dispatched: ''
            }
        }
    'interval-filters' -
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

	Note: 'dbDateFormat' is REQUIRED if the filter values are dates. The given format has to match the database date format

### Publishes

- `php artisan vendor:publish --tag=datatable-component` - the VueJS component file
- `php artisan vendor:publish --tag=datatable-options` - the json options file
- `php artisan vendor:publish --tag=datatable-lang` - the default lang files
- `php artisan vendor:publish --tag=datatable-class` - the abstract TableStructure class that must be extended when creating specific structures
- `php artisan vendor:publish --tag=enso-update` - a common alias for when wanting to update the VueJS component, 
once a newer version is released.


### Contributions

...are welcome