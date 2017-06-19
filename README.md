# Data Table
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ee67de45d1f14dbd98eb72c8cf972902)](https://www.codacy.com/app/laravel-enso/DataTable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/DataTable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85495802/shield?branch=master)](https://styleci.io/repos/85495802)
[![Total Downloads](https://poser.pugx.org/laravel-enso/datatable/downloads)](https://packagist.org/packages/laravel-enso/datatable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/datatable/version)](https://packagist.org/packages/laravel-enso/datatable)

DataTable builder for DataTable.net library with server-side processing. Includes a vue component.

### Demo

soon.

### Installation Steps

1. Add `LaravelEnso\DataTable\DataServiceProvider::class` to `config/app.php`. (included if you use LaravelEnso/core)

2. Publish the vue component with `php artisan vendor:publish --tag=datatable-component`.

3. Publish the sample structure class with `php artisan vendor:publish --tag=datatable-structure`.

4. Include the vue component in your app.js.

5. Run gulp.

6. In your blade add:

```
<data-table source="/indexRoute">
    <span slot="data-table-title">{{ __("Title") }}</span>
    <span slot="data-table-totals">{{ __("Totals") }}</span>
    @include('laravel-enso/core::partials.modal')
</data-table>
```

7. In your controller add `use DataTable` trait. This adds in your controller two helper methods that will manage the builder:
	- initTable
	- getTableData

8. In the controller you must define a method for the query builder like this:

```
public function getTableQuery()
{
    return Model::select(\DB::raw('id as DT_RowId, attribute1, ..., attributeN'));
}
```

9. Also in the controller add `protected $tableStructureClass = TableStructure::class`.

10. In your routes files add two routes for the helper methods, and name them `indexRoute.initTable` and `indexRoute.getTableData`.

11. Configure the table from the structure class.

12. Enjoy if you still have the mood :)

### Options

	`source` - required, must reference the controllers index route.
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

### Contributions

...are welcome