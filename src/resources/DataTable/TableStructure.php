<?php

namespace App\DataTable;

namespace TableStructure;

use App\Utils\DataTable\Abstracts\TableStructure;

class ClassName extends TableStructure
{
    public function __construct()
    {
        $this->data = [
            /* current number for each line, with the header name
             * If given, it will be appended as the first column of the table
             */
            'crtNo' => 'app.crtNo',
            /* column for buttons with available actions. Used for both standard
             * actions - create, view, edit, delete - and custom actions.
             * Note: the buttons for standard actions are added automatically depending
             * on permissions and do not need to be specified here or elsewhere.
             */
            'actionButtons' => 'app.actions',
            /* list of action buttons for custom actions
             * cssSelectorClass is used for adding the button event listener
             * cssClass is used for styling the custom button
             * event is the event triggered by the button
             * route is OPTIONAL, and if given, it will be checked for permission i.e. if the user doesn't have
             * the necessary permission, that specific button is not drawn
             */
            'customActionButtons' => [
                ['class' => 'btn-success fa fa-info-circle', 'event'=>'custom-event', 'route' => 'route.getData'],
            ],
            /* columns where custom rendering is applied
             * Note: a 'customRender' method needs to exist in the parent vue instance
             */
            'render' => [columnIndexArray],
            /* column number where the total value is displayed
             */
            'totals' => [columnIndexArray],
            /* computed responsive priority will be 1 for first column
             * and will increment with one for each consecutive column
             */
            'responsivePriority' => [columnIndexArray],
            /* list of columns that are not searchable,
             * such as columns that are translated */
            'notSearchable' => [columnIndexArray],
            /* list of columns that you don't want to be sortable */
            'notSortable' => [columnIndexArray],
            /* list of editable columns
             * Note:  the  parameter is needed, and
             * only attributes of this model are editable i.e. you can't
             * edit attributes of 'joined' models/tables
             */
            'editable' => [columnIndexArray],
            /* if none is given, by default, 'lBfrtip' is used.
             * See datatables.net documentation */
            'dom' => 'lBfrtip',
            /* table header alignment. The dt-head-* class is used,
             * i.e. dt-head-center in this example  */
            'headerAlign' => 'left center right',
            /* table body alignment. The dt-body-center class is used in this example
             */
            'bodyAlign' => 'left center right',
            /* custom class for the <table> element */
            'tableClass' => '',
            /* list of columns whose values should be displayed translated.
             * Note: in order to work it needs a CustomEnum class
             */
            'enumMappings' => [
                'status' => 'StatusesEnum',
            ],
            /* list of columns to be displayed
             */
            'columns' => [
                0 => [
                    'label' => 'labelName',
                    'data'  => 'attributeName',
                    'name'  => 'attributeSource',
                    'class' => 'className',
                ],
            ],
        ];
    }
}
