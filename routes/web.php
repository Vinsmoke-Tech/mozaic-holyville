<?php

use App\Http\Controllers\AcctBalanceSheetReportController;
use App\Http\Controllers\AcctAccountController;
use App\Http\Controllers\CoreRecipeController;
use App\Http\Controllers\AcctAccountSettingController;
use App\Http\Controllers\AcctDisbursementReportController;
use App\Http\Controllers\AcctJournalMemorialController;
use App\Http\Controllers\AcctLedgerReportController;
use App\Http\Controllers\AcctProfitLossReportController;
use App\Http\Controllers\AcctProfitLossYearReportController;
use App\Http\Controllers\AcctProfitDayReportController;
use App\Http\Controllers\AcctReceiptsController;
use App\Http\Controllers\AcctReceiptsReportController;
use App\Http\Controllers\GeneralLedgerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\SystemUserGroupController;
use App\Http\Controllers\InvtItemCategoryController;
use App\Http\Controllers\InvtItemController;
use App\Http\Controllers\InvtItemUnitController;
use App\Http\Controllers\InvtStockAdjustmentController;
use App\Http\Controllers\InvtStockAdjustmentReportController;
use App\Http\Controllers\InvtStockOutController;
use App\Http\Controllers\InvtStockTransferController;
use App\Http\Controllers\InvtWarehouseController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\PurchaseInvoicebyItemReportController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseInvoiceReportController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseReturnReportController;
use App\Http\Controllers\ConsigneeReportController;
use App\Http\Controllers\SalesConsigneeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SalesCustomerController;
use App\Http\Controllers\SalesInvoicebyItemReportController;
use App\Http\Controllers\SalesInvoiceByUserReportController;
use App\Http\Controllers\SalesInvoiceByYearReportController;
use App\Http\Controllers\SalesInvoicePPNReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesInvoiceReportController;
use App\Http\Controllers\SystemPPNController;
use App\Http\Controllers\JournalCategoriesController;
use App\Http\Controllers\JournalCategoriesFormulaController;
use App\Http\Controllers\JournalSpecialController;
use App\Http\Controllers\SalesCollectionController;




use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/select-item-stock/{category_id}/{unit_id}/{item_id}',[HomeController::class, 'selectItemStock']);
Route::get('/select-item/{id}',[HomeController::class, 'selectItem']);
Route::get('/select-item-unit/{id}',[HomeController::class, 'selectItemUnit']);
Route::get('/option-item/{item_category_id}',[PublicController::class, 'optionItem']);
Route::get('/option-item-unit/{item_id}',[PublicController::class, 'optionItemUnit']);
Route::get('/get-item-price/{item_id}',[PublicController::class, 'getItemPrice']);
Route::get('/get-item-sales_price/{item_id}',[PublicController::class, 'getItemSalesPrice']);


Route::get('/consignee-report', [ConsigneeReportController::class, 'index'])->name('consignee-report');
Route::post('/consignee-report/filter',[ConsigneeReportController::class, 'filterConsigneeReport'])->name('filter-consignee-report');
Route::get('/consignee-report/filter-reset',[ConsigneeReportController::class, 'filterResetConsigneeReport'])->name('filter-reset-consignee-report');
Route::get('/consignee-report/print',[ConsigneeReportController::class, 'printConsigneeReport'])->name('print-consignee-report');
Route::get('/consignee-report/export',[ConsigneeReportController::class, 'exportConsigneeReport'])->name('export-consignee-report');

Route::get('/select-item-cost/{item_packge_id}',[HomeController::class, 'selectItemCost']);
Route::get('/select-item-price/{item_packge_id}',[HomeController::class, 'selectItemPrice']);
Route::get('/get-margin-category/{item_packge_id}',[HomeController::class, 'getMarginCategory']);
Route::get('/amount/sales-invoice/{day}', [HomeController::class, 'getAmountSalesInvoice']);
Route::get('/amount/purchase-invoice/{day}', [HomeController::class, 'getAmountPurchaseInvoice']);
Route::get('/select-sales/{item}',[SalesInvoiceController::class, 'selectSalesInvoice']);
Route::get('/select-sales/{item_name}/{unit_id}/{order}',[SalesInvoiceController::class, 'selectItemNameSalesInvoice']);
Route::get('/sales-invoice/change-qty/{item}/{qty}',[SalesInvoiceController::class, 'changeQtySalesInvoice']);
Route::get('/select-item-auto/{item_id}',[HomeController::class, 'selectItemAuto']);
Route::get('/select-item-category/{id}',[HomeController::class, 'selectItemCategory']);

Route::get('/item-unit',[InvtItemUnitController::class, 'index'])->name('item-unit');
Route::post('/item-unit/get-item-unit',[InvtItemUnitController::class, 'getItemUnit'])->name('get-item-unit');
Route::get('/item-unit/add',[InvtItemUnitController::class, 'addInvtItemUnit'])->name('add-item-unit');
Route::post('/item-unit/elements-add',[InvtItemUnitController::class, 'elementAddElementsInvtItemUnit'])->name('add-item-unit-elements');
Route::post('/item-unit/process-add',[InvtItemUnitController::class,'processAddElementsInvtItemUnit'])->name('process-add');
Route::get('/item-unit/reset-add',[InvtItemUnitController::class, 'addReset'])->name('add-reset-item-unit');
Route::get('/item-unit/edit/{item_unit_id}', [InvtItemUnitController::class, 'editInvtItemUnit'])->name('edit-item-unit');
Route::post('/item-unit/process-edit-item-unit', [InvtItemUnitController::class, 'processEditInvtItemUnit'])->name('process-edit-item-unit');
Route::get('/item-unit/delete/{item_unit_id}', [InvtItemUnitController::class, 'deleteInvtItemUnit'])->name('delete-item-unit');

Route::get('/item-category',[InvtItemCategoryController::class, 'index'])->name('item-category');
Route::get('/item-category/add',[InvtItemCategoryController::class, 'addItemCategory'])->name('add-item-category');
Route::post('/item-category/elements-add',[InvtItemCategoryController::class, 'elementsAddItemCategory'])->name('elements-add-category');
Route::post('/item-category/process-add-category', [InvtItemCategoryController::class, 'processAddItemCategory'])->name('process-add-item-category');
Route::get('/item-category/reset-add',[InvtItemCategoryController::class, 'addReset'])->name('add-reset-category');
Route::get('/item-category/edit-category/{item_category_id}', [InvtItemCategoryController::class, 'editItemCategory'])->name('edit-item-category');
Route::post('/item-category/process-edit-item-category', [InvtItemCategoryController::class, 'processEditItemCategory'])->name('process-edit-item-category');
Route::get('/item-category/delete-category/{item_category_id}', [InvtItemCategoryController::class, 'deleteItemCategory'])->name('delete-item-category');

Route::get('/item',[InvtItemController::class, 'index'])->name('item');
Route::get('/item/add-item', [InvtItemController::class, 'addItem'])->name('add-item');
Route::get('/item/add-reset', [InvtItemController::class, 'addResetItem'])->name('add-reset-item');
Route::post('/item/add-item-elements', [InvtItemController::class, 'addItemElements'])->name('add-item-elements');
Route::post('/item/process-add-item', [InvtItemController::class,'processAddItem'])->name('process-add-item');
Route::get('/item/edit-item/{item_id}', [InvtItemController::class, 'editItem'])->name('edit-item');
Route::post('/item/process-edit-item', [InvtItemController::class, 'processEditItem'])->name('process-edit-item');
Route::get('/item/delete-item/{item_id}', [InvtItemController::class, 'deleteItem'])->name('delete-item');

Route::get('/recipe',[CoreRecipeController::class, 'index'])->name('recipe');
Route::get('/recipe/add-recipe/{item_id}', [CoreRecipeController::class, 'addRecipe'])->name('add-recipe');
Route::get('/recipe/add-reset', [CoreRecipeController::class, 'addResetRecipe'])->name('add-reset-recipe');
Route::post('/recipe/add-recipe-elements', [CoreRecipeController::class, 'addRecipeElements'])->name('add-recipe-elements');
Route::post('/recipe/process-add-recipe', [CoreRecipeController::class,'processAddRecipe'])->name('process-add-recipe');
Route::post('/recipe/process-recipe', [CoreRecipeController::class,'processRecipe'])->name('process-recipe');
Route::get('/recipe/edit-recipe/{recipe_id}', [CoreRecipeController::class, 'editRecipe'])->name('edit-recipe');
Route::post('/recipe/process-edit-recipe', [CoreRecipeController::class, 'processEditRecipe'])->name('process-edit-recipe');
Route::get('/recipe/delete-recipe/{recipe_id}', [CoreRecipeController::class, 'deleteRecipe'])->name('delete-recipe');


Route::get('/warehouse',[InvtWarehouseController::class, 'index'])->name('warehouse');
Route::get('/warehouse/add-warehouse', [InvtWarehouseController::class, 'addWarehouse'])->name('add-warehouse');
Route::get('/warehouse/add-reset', [InvtWarehouseController::class, 'addResetWarehouse'])->name('add-reset-warehouse');
Route::post('/warehouse/add-warehouse-elements', [InvtWarehouseController::class, 'addElementsWarehouse'])->name('add-warehouse-elements');
Route::post('/warehouse/process-add-warehouse', [InvtWarehouseController::class,'processAddWarehouse'])->name('process-add-warehouse');
Route::get('/warehouse/edit-warehouse/{warehouse_id}',[InvtWarehouseController::class, 'editWarehouse'])->name('edit-warehouse');
Route::post('/warehouse/process-edit-warehouse', [InvtWarehouseController::class, 'processEditWarehouse'])->name('process-edit-warehouse');
Route::get('/warehouse/delete-warehouse/{warehouse_id}', [InvtWarehouseController::class, 'deleteWarehouse'])->name('delete-warehouse');


Route::get('/purchase-return', [PurchaseReturnController::class, 'index'])->name('purchase-return');
Route::get('/purchase-return/add', [PurchaseReturnController::class, 'addPurchaseReturn'])->name('add-purchase-return');
Route::get('/purchase-return/add-reset', [PurchaseReturnController::class, 'addResetPurchaseReturn'])->name('add-reset-purchase-return');
Route::post('/purchase-return/add-elements', [PurchaseReturnController::class, 'addElementsPurchaseReturn'])->name('add-elements-purchase-return');
Route::post('/purchase-return/process-add',[PurchaseReturnController::class, 'processAddPurchaseReturn'])->name('process-add-purchase-return');
Route::post('/purchase-return/add-array',[PurchaseReturnController::class, 'addArrayPurchaseReturn'])->name('add-array-purchase-return');
Route::get('/purchase-return/delete-array/{record_id}',[PurchaseReturnController::class, 'deleteArrayPurchaseReturn'])->name('delete-array-purchase-return');
Route::get('/purchase-return/detail/{purchase_return_id}',[PurchaseReturnController::class, 'detailPurchaseReturn'])->name('detail-purchase-return');
Route::post('/purchase-return/filter',[PurchaseReturnController::class, 'filterPurchaseReturn'])->name('filter-purchase-return');
Route::get('/purchase-return/filter-reset',[PurchaseReturnController::class, 'filterResetPurchaseReturn'])->name('filter-reset-purchase-return');
Route::get('/purchase-return/edit', [PurchaseReturnController::class, 'editPurchaseReturn'])->name('edit-purchase-return');
Route::post('/purchase-return/process-edit',[PurchaseReturnController::class, 'processeditPurchaseReturn'])->name('process-edit-purchase-return');
Route::get('/purchase-return/delete', [PurchaseReturnController::class, 'deletePurchaseReturn'])->name('delete-purchase-return');

Route::get('/sales-invoice',[SalesInvoiceController::class, 'index'])->name('sales-invoice');
Route::get('/sales-invoice/add', [SalesInvoiceController::class,'addSalesInvoice'])->name('add-sales-invoice');
Route::post('/sales-invoice/add-elements', [SalesInvoiceController::class,'elements_add'])->name('add-elements-sales-invoice');
Route::post('/sales-invoice/process-add', [SalesInvoiceController::class, 'processAddSalesInvoice'])->name('process-add-sales-invoice');
Route::get('/sales-invoice/reset-add',[SalesInvoiceController::class, 'resetSalesInvoice'])->name('add-reset-sales-invoice');
Route::post('/sales-invoice/add-array',[SalesInvoiceController::class,'addArraySalesInvoice'])->name('add-array-sales-invoice');
Route::get('/sales-invoice/delete-array/{record_id}',[SalesInvoiceController::class,'deleteArraySalesInvoice'])->name('delete-array-sales-invoice');
Route::get('/sales-invoice/detail/{sales_invoice_id}',[SalesInvoiceController::class, 'detailSalesInvoice'])->name('detail-sales-invoice');
Route::get('/sales-invoice/delete/{sales_invoice_id}',[SalesInvoiceController::class, 'deleteSalesInvoice'])->name('delete-sales-invoice');
Route::get('/sales-invoice/filter-reset',[SalesInvoiceController::class, 'filterResetSalesInvoice'])->name('filter-reset-sales-invoice');
Route::post('/sales-invoice/filter',[SalesInvoiceController::class, 'filterSalesInvoice'])->name('filter-sales-invoice');

Route::get('/sales-consignee',[SalesConsigneeController::class, 'index'])->name('sales-consignee');
Route::get('/sales-consignee/add', [SalesConsigneeController::class,'addSalesConsignee'])->name('add-sales-consignee');
Route::post('/sales-consignee/add-elements', [SalesConsigneeController::class,'addElementsSalesConsignee'])->name('add-elements-sales-consignee');
Route::post('/sales-consignee/process-add', [SalesConsigneeController::class, 'processAddSalesConsignee'])->name('process-add-sales-consignee');
Route::get('/sales-consignee/reset-add',[SalesConsigneeController::class, 'resetSalesConsignee'])->name('add-reset-sales-consignee');
Route::post('/sales-consignee/add-array',[SalesConsigneeController::class,'addArraySalesConsignee'])->name('add-array-sales-consignee');
Route::get('/sales-consignee/delete-array/{record_id}',[SalesConsigneeController::class,'deleteArraySalesConsignee'])->name('delete-array-sales-consignee');
Route::get('/sales-consignee/detail/{sales_invoice_id}',[SalesConsigneeController::class, 'detailSalesConsignee'])->name('detail-sales-consignee');
Route::get('/sales-consignee/delete/{sales_invoice_id}',[SalesConsigneeController::class, 'deleteSalesConsignee'])->name('delete-sales-consignee');
Route::get('/sales-consignee/filter-reset',[SalesConsigneeController::class, 'filterResetSalesConsignee'])->name('filter-reset-sales-consignee');
Route::post('/sales-consignee/filter',[SalesConsigneeController::class, 'filterSalesConsignee'])->name('filter-sales-consignee');

Route::get('/purchase-invoice', [PurchaseInvoiceController::class, 'index'])->name('purchase-invoice');
Route::get('/purchase-invoice/add', [PurchaseInvoiceController::class, 'addPurchaseInvoice'])->name('add-purchase-invoice');
Route::get('/purchase-invoice/add-reset', [PurchaseInvoiceController::class, 'addResetPurchaseInvoice'])->name('add-reset-purchase-invoice');
Route::post('/purchase-invoice/add-elements', [PurchaseInvoiceController::class, 'addElementsPurchaseInvoice'])->name('add-elements-purchase-invoice');
Route::post('/purchase-invoice/add-array',[PurchaseInvoiceController::class, 'addArrayPurchaseInvoice'])->name('add-array-purchase-invoice');
Route::get('/purchase-invoice/delete-array/{record_id}', [PurchaseInvoiceController::class, 'deleteArrayPurchaseInvoice'])->name('delete-array-purchase-invoice');
Route::post('/purchase-invoice/process-add', [PurchaseInvoiceController::class, 'processAddPurchaseInvoice'])->name('process-add-purchase-invoice');
Route::get('/purchase-invoice/detail/{purchase_invoice_id}',[PurchaseInvoiceController::class, 'detailPurchaseInvoice'])->name('detail-purchase-invoice');
Route::get('/purchase-invoice/delete/{purchase_invoice_id}',[PurchaseInvoiceController::class, 'processDeletePurchaseInvoice'])->name('delete-purchase-invoice');
Route::post('/purchase-invoice/filter', [PurchaseInvoiceController::class,'filterPurchaseInvoice'])->name('filter-purchase-invoice');
Route::get('/purchase-invoice/filter-reset', [PurchaseInvoiceController::class,'filterResetPurchaseInvoice'])->name('filter-reset-purchase-invoice');
Route::post('/purchase-invoice/paid-purchase-invoice',[purchaseInvoiceController::class, 'addPaidPurchaseInvoice'])->name('add-paid-purchase-invoice');
Route::get('/purchase-invoice/paid-detail-purchase-invoice/{purchase_invoice_id}',[purchaseInvoiceController::class, 'detailPaidpurchaseInvoice'])->name('paid-detail-purchase-invoice');
Route::get('/purchase-invoice/delete-consignee/{purchase_invoice_id}',[PurchaseInvoiceController::class, 'processDeleteConsignee'])->name('delete-consignee');
Route::get('/purchase-invoice/reset-consignee/{purchase_invoice_id}',[PurchaseInvoiceController::class, 'processResetConsignee'])->name('reset-consignee');



Route::get('/system-user', [SystemUserController::class, 'index'])->name('system-user');
Route::get('/system-user/add', [SystemUserController::class, 'addSystemUser'])->name('add-system-user');
Route::post('/system-user/process-add-system-user', [SystemUserController::class, 'processAddSystemUser'])->name('process-add-system-user');
Route::get('/system-user/edit/{user_id}', [SystemUserController::class, 'editSystemUser'])->name('edit-system-user');
Route::post('/system-user/process-edit-system-user', [SystemUserController::class, 'processEditSystemUser'])->name('process-edit-system-user');
Route::get('/system-user/delete-system-user/{user_id}', [SystemUserController::class, 'deleteSystemUser'])->name('delete-system-user');
Route::get('/system-user/change-password/{user_id}  ', [SystemUserController::class, 'changePassword'])->name('change-password');
Route::post('/system-user/process-change-password', [SystemUserController::class, 'processChangePassword'])->name('process-change-password');


Route::get('/system-user-group', [SystemUserGroupController::class, 'index'])->name('system-user-group');
Route::get('/system-user-group/add', [SystemUserGroupController::class, 'addSystemUserGroup'])->name('add-system-user-group');
Route::post('/system-user-group/process-add-system-user-group', [SystemUserGroupController::class, 'processAddSystemUserGroup'])->name('process-add-system-user-group');
Route::get('/system-user-group/edit/{user_id}', [SystemUserGroupController::class, 'editSystemUserGroup'])->name('edit-system-user-group');
Route::post('/system-user-group/process-edit-system-user-group', [SystemUserGroupController::class, 'processEditSystemUserGroup'])->name('process-edit-system-user-group');
Route::get('/system-user-group/delete-system-user-group/{user_id}', [SystemUserGroupController::class, 'deleteSystemUserGroup'])->name('delete-system-user-group');

Route::get('/stock-adjustment',[InvtStockAdjustmentController::class,'index'])->name('stock-adjustment');
Route::get('/stock-adjustment/add', [InvtStockAdjustmentController::class,'addStockAdjustment'])->name('add-stock-adjustment');
Route::get('/stock-adjustment/add-reset', [InvtStockAdjustmentController::class,'addReset'])->name('add-reset-stock-adjustment');
Route::get('/stock-adjustment/list-reset', [InvtStockAdjustmentController::class,'listReset'])->name('list-reset-stock-adjustment');
Route::post('/stock-adjustment/add-elements',[InvtStockAdjustmentController::class, 'addElementsStockAdjustment'])->name('add-elements-stock-adjustment');
Route::post('/stock-adjustment/filter-add', [InvtStockAdjustmentController::class, 'filterAddStockAdjustment'])->name('filter-add-stock-adjustment');
Route::post('/stock-adjustment/filter-list', [InvtStockAdjustmentController::class, 'filterListStockAdjustment'])->name('filter-list-stock-adjustment');
Route::post('/stock-adjustment/process-add', [InvtStockAdjustmentController::class, 'processAddStockAdjustment'])->name('process-add-stock-adjustment');
Route::get('/stock-adjustment/detail/{stock_adjustment_id}',[InvtStockAdjustmentController::class, 'detailStockAdjustment'])->name('detail-stock-adjustment');

Route::get('/stock-adjustment-report',[InvtStockAdjustmentReportController::class, 'index'])->name('stock-adjustment-report');
Route::post('/stock-adjustment-report/filter',[InvtStockAdjustmentReportController::class, 'filterStockAdjustmentReport'])->name('stock-adjustment-report-filter');
Route::get('/stock-adjustment-report/reset',[InvtStockAdjustmentReportController::class, 'resetStockAdjustmentReport'])->name('stock-adjustment-report-reset');
Route::get('/stock-adjustment-report/print',[InvtStockAdjustmentReportController::class, 'printStockAdjustmentReport'])->name('stock-adjustment-report-print');
Route::get('/stock-adjustment-report/export',[InvtStockAdjustmentReportController::class, 'exportStockAdjustmentReport'])->name('stock-adjustment-report-export');
Route::get('/stock-adjustment-report/getlastbalance',[InvtStockAdjustmentReportController::class, 'getLastBalanceStock'])->name('stock-adjustment-report-lastbalance');


Route::get('/purchase-invoice-report', [PurchaseInvoiceReportController::class, 'index'])->name('purchase-invoice-report');
Route::post('/purchase-invoice-report/filter',[PurchaseInvoiceReportController::class, 'filterPurchaseInvoiceReport'])->name('filter-purchase-invoice-report');
Route::get('/purchase-invoice-report/filter-reset',[PurchaseInvoiceReportController::class, 'filterResetPurchaseInvoiceReport'])->name('filter-reset-purchase-invoice-report');
Route::get('/purchase-invoice-report/print',[PurchaseInvoiceReportController::class, 'printPurchaseInvoiceReport'])->name('print-purchase-invoice-report');
Route::get('/purchase-invoice-report/export',[PurchaseInvoiceReportController::class, 'exportPurchaseInvoiceReport'])->name('export-purchase-invoice-report');

Route::get('/purchase-return-report',[PurchaseReturnReportController::class, 'index'])->name('purchase-return-report');
Route::post('/purchase-return-report/filter',[PurchaseReturnReportController::class, 'filterPurchaseReturnReport'])->name('filter-purchase-return-report');
Route::get('/purchase-return-report/filter-reset',[PurchaseReturnReportController::class, 'filterResetPurchaseReturnReport'])->name('filter-reset-purchase-return-report');
Route::get('/purchase-return-report/print',[PurchaseReturnReportController::class, 'printPurchaseReturnReport'])->name('print-purchase-return-report');
Route::get('/purchase-return-report/export',[PurchaseReturnReportController::class, 'exportPurchaseReturnReport'])->name('export-purchase-return-report');

Route::get('/purchase-invoice-by-item-report',[PurchaseInvoicebyItemReportController::class, 'index'])->name('purchase-invoice-by-item-report');
Route::post('/purchase-invoice-by-item-report/filter',[PurchaseInvoicebyItemReportController::class, 'filterPurchaseInvoicebyItemReport'])->name('filter-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/filter-reset',[PurchaseInvoicebyItemReportController::class, 'filterResetPurchaseInvoicebyItemReport'])->name('filter-reset-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/print',[PurchaseInvoicebyItemReportController::class, 'printPurchaseInvoicebyItemReport'])->name('print-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/export',[PurchaseInvoicebyItemReportController::class, 'exportPurchaseInvoicebyItemReport'])->name('export-purchase-invoice-by-item-report');

Route::get('/sales-invoice-report', [SalesInvoiceReportController::class, 'index'])->name('sales-invoice-report');
Route::post('/sales-invoice-report/filter', [SalesInvoiceReportController::class, 'filterSalesInvoiceReport'])->name('filter-sales-invoice-report');
Route::get('/sales-invoice-report/filter-reset', [SalesInvoiceReportController::class, 'filterResetSalesInvoiceReport'])->name('filter-reset-sales-invoice-report');
Route::get('/sales-invoice-report/print', [SalesInvoiceReportController::class, 'printSalesInvoiceReport'])->name('print-sales-invoice-report');
Route::get('/sales-invoice-report/export', [SalesInvoiceReportController::class, 'exportSalesInvoiceReport'])->name('export-sales-invoice-report');

Route::get('/sales-invoice-by-item-report',[SalesInvoicebyItemReportController::class, 'index'])->name('sales-invoice-by-item-report');
Route::post('/sales-invoice-by-item-report/filter',[SalesInvoicebyItemReportController::class, 'filterSalesInvoicebyItemReport'])->name('filter-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/filter-reset',[SalesInvoicebyItemReportController::class, 'filterResetSalesInvoicebyItemReport'])->name('filter-reset-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/print',[SalesInvoicebyItemReportController::class, 'printSalesInvoicebyItemReport'])->name('print-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/export',[SalesInvoicebyItemReportController::class, 'exportSalesInvoicebyItemReport'])->name('export-sales-invoice-by-item-report');

Route::get('/sales-invoice-by-item-report/not-sold',[SalesInvoicebyItemReportController::class, 'notSold'])->name('sales-invoice-by-item-not-sold-report');
Route::post('/sales-invoice-by-item-report/filter-not-sold',[SalesInvoicebyItemReportController::class, 'filterSalesInvoicebyItemNotSoldReport'])->name('filter-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/not-sold-filter-reset',[SalesInvoicebyItemReportController::class, 'filterResetSalesInvoicebyItemNotSoldReport'])->name('filter-reset-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/print-not-sold',[SalesInvoicebyItemReportController::class, 'printSalesInvoicebyItemNotSoldReport'])->name('print-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/export-not-sold',[SalesInvoicebyItemReportController::class, 'exportSalesInvoicebyItemNotSoldReport'])->name('export-sales-invoice-by-item-not-sold-report');

Route::get('/sales-invoice-by-year-report',[SalesInvoiceByYearReportController::class, 'index'])->name('sales-invoice-by-year-report');
Route::post('/sales-invoice-by-year-report/filter',[SalesInvoiceByYearReportController::class, 'filterSalesInvoicebyYearReport'])->name('filter-sales-invoice-by-year-report');
Route::get('/sales-invoice-by-year-report/print',[SalesInvoiceByYearReportController::class, 'printSalesInvoicebyYearReport'])->name('print-sales-invoice-by-year-report');
Route::get('/sales-invoice-by-year-report/export',[SalesInvoiceByYearReportController::class, 'exportSalesInvoicebyYearReport'])->name('export-sales-invoice-by-year-report');

Route::get('/sales-invoice-by-user-report',[SalesInvoiceByUserReportController::class, 'index'])->name('sales-invoice-by-user-report');
Route::post('/sales-invoice-by-user-report/filter',[SalesInvoicebyUserReportController::class, 'filterSalesInvoicebyUserReport'])->name('filter-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/filter-reset',[SalesInvoicebyUserReportController::class, 'filterResetSalesInvoicebyUserReport'])->name('filter-reset-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/print',[SalesInvoicebyUserReportController::class, 'printSalesInvoicebyUserReport'])->name('print-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/export',[SalesInvoicebyUserReportController::class, 'exportSalesInvoicebyUserReport'])->name('export-sales-invoice-by-user-report');

Route::get('/sales-invoice-ppn-report',[SalesInvoicePPNReportController::class, 'index'])->name('sales-invoice-ppn-report');
Route::post('/sales-invoice-ppn-report/filter',[SalesInvoicePPNReportController::class, 'filterSalesInvoicePPNReport'])->name('filter-sales-invoice-ppn-report');
Route::get('/sales-invoice-ppn-report/filter-reset',[SalesInvoicePPNReportController::class, 'filterResetSalesInvoicePPNReport'])->name('filter-reset-sales-invoice-ppn-report');
Route::get('/sales-invoice-ppn-report/print',[SalesInvoicePPNReportController::class, 'printSalesInvoicePPNReport'])->name('print-sales-invoice-ppn-report');
Route::get('/sales-invoice-ppn-report/export',[SalesInvoicePPNReportController::class, 'exportSalesInvoicePPNReport'])->name('export-sales-invoice-ppn-report');

Route::get('/acct-account', [AcctAccountController::class, 'index'])->name('acct-account');
Route::get('/acct-account/add',[AcctAccountController::class, 'addAcctAccount'])->name('add-acct-account');
Route::post('/acct-account/process-add',[AcctAccountController::class, 'processAddAcctAccount'])->name('process-add-acct-account');
Route::post('/acct-account/add-elements',[AcctAccountController::class, 'addElementsAcctAccount'])->name('add-elements-acct-account');
Route::get('/acct-account/add-reset',[AcctAccountController::class, 'addResetAcctAccount'])->name('add-reset-acct-account');
Route::get('/acct-account/edit/{account_id}',[AcctAccountController::class, 'editAcctAccount'])->name('edit-acct-account');
Route::post('/acct-account/process-edit',[AcctAccountController::class, 'processEditAcctAccount'])->name('process-edit-acct-account');
Route::get('/acct-account/delete/{account_id}',[AcctAccountController::class, 'deleteAcctAccount'])->name('delete-edit-acct-account');

Route::get('/acct-account-setting',[AcctAccountSettingController::class, 'index'])->name('acct-account-setting');
Route::post('/acct-account-setting/process-add',[AcctAccountSettingController::class, 'processAddAcctAccountSetting'])->name('process-add-acct-account-setting');

Route::get('/journal-voucher', [JournalVoucherController::class, 'index'])->name('journal-voucher');
Route::get('/journal-voucher/add', [JournalVoucherController::class, 'addJournalVoucher'])->name('add-journal-voucher');
Route::post('/journal-voucher/add-array', [JournalVoucherController::class, 'addArrayJournalVoucher'])->name('add-array-journal-voucher');
Route::post('/journal-voucher/add-elements', [JournalVoucherController::class, 'addElementsJournalVoucher'])->name('add-elements-journal-voucher');
Route::get('/journal-voucher/reset-add', [JournalVoucherController::class, 'resetAddJournalVoucher'])->name('reset-add-journal-voucher');
Route::post('/journal-voucher/process-add', [JournalVoucherController::class, 'processAddJournalVoucher'])->name('process-add-journal-voucher');
Route::post('/journal-voucher/filter', [JournalVoucherController::class, 'filterJournalVoucher'])->name('filter-journal-voucher');
Route::get('/journal-voucher/reset-filter', [JournalVoucherController::class, 'resetFilterJournalVoucher'])->name('reset-filter-journal-voucher');
Route::get('/journal-voucher/print/{journal_voucher_id}', [JournalVoucherController::class, 'printJournalVoucher'])->name('print-journal-voucher');
Route::get('/journal-voucher/printall', [JournalVoucherController::class, 'printJournalVoucherAll'])->name('print-journal-voucher-all');
Route::get('/journal-voucher/printdebit', [JournalVoucherController::class, 'printJournalVoucherDebit'])->name('print-journal-voucher-debit');
Route::get('/journal-voucher/printcredit', [JournalVoucherController::class, 'printJournalVoucherCredit'])->name('print-journal-voucher-credit');
Route::get('/journal-voucher/reverse/{journal_voucher_id}', [JournalVoucherController::class, 'reverseJournalVoucher'])->name('reverse-journal-voucher');

Route::get('/journal-special', [JournalSpecialController::class, 'index'])->name('journal-special');
Route::get('/journal-special/add', [JournalSpecialController::class, 'addJournalSpecial'])->name('add-journal-special');
Route::post('/journal-special/add-array', [JournalSpecialController::class, 'addArrayJournalSpecial'])->name('add-array-journal-special');
Route::post('/journal-special/add-elements', [JournalSpecialController::class, 'addElementsJournalSpecial'])->name('add-elements-journal-special');
Route::get('/journal-special/reset-add', [JournalSpecialController::class, 'resetAddJournalSpecial'])->name('reset-add-journal-special');
Route::post('/journal-special/process-add', [JournalSpecialController::class, 'processAddJournalSpecial'])->name('process-add-journal-special');
Route::post('/journal-special/filter', [JournalSpecialController::class, 'filterJournalSpecial'])->name('filter-journal-special');
Route::get('/journal-special/reset-filter', [JournalSpecialController::class, 'resetFilterJournalSpecial'])->name('reset-filter-journal-special');
Route::get('/journal-special/print/{journal_voucher_id}', [JournalSpecialController::class, 'printJournalSpecial'])->name('print-journal-special');
Route::get('/journal-special/printall', [JournalSpecialController::class, 'printJournalSpecialAll'])->name('print-journal-special-all');
Route::get('/journal-special/printdebit', [JournalSpecialController::class, 'printJournalSpecialDebit'])->name('print-journal-special-debit');
Route::get('/journal-special/printcredit', [JournalSpecialController::class, 'printJournalSpecialCredit'])->name('print-journal-special-credit');
Route::get('/journal-special/reverse/{journal_voucher_id}', [JournalSpecialController::class, 'reverseJournalSpecial'])->name('reverse-journal-special');


Route::get('/ledger-report',[AcctLedgerReportController::class, 'index'])->name('ledger-report');
Route::post('/ledger-report/filter',[AcctLedgerReportController::class, 'filterLedgerReport'])->name('filter-ledger-report');
Route::get('/ledger-report/reset-filter',[AcctLedgerReportController::class, 'resetFilterLedgerReport'])->name('reset-filter-ledger-report');
Route::get('/ledger-report/print',[AcctLedgerReportController::class, 'printLedgerReport'])->name('print-ledger-report');
Route::get('/ledger-report/export',[AcctLedgerReportController::class, 'exportLedgerReport'])->name('export-ledger-report');

Route::get('/journal-memorial',[AcctJournalMemorialController::class, 'index'])->name('journal-memorial');
Route::post('/journal-memorial/filter',[AcctJournalMemorialController::class, 'filterJournalMemorial'])->name('filter-journal-memorial');
Route::get('/journal-memorial/reset-filter',[AcctJournalMemorialController::class, 'resetFilterJournalMemorial'])->name('reset-filter-journal-memorial');

Route::get('/profit-loss-report',[AcctProfitLossReportController::class, 'index'])->name('profit-loss-report');
Route::post('/profit-loss-report/filter',[AcctProfitLossReportController::class, 'filterProfitLossReport'])->name('filter-profit-loss-report');
Route::get('/profit-loss-report/reset-filter',[AcctProfitLossReportController::class, 'resetFilterProfitLossReport'])->name('reset-filter-profit-loss-report');
Route::get('/profit-loss-report/print',[AcctProfitLossReportController::class, 'printProfitLossReport'])->name('print-profit-loss-report');
Route::get('/profit-loss-report/export',[AcctProfitLossReportController::class, 'exportProfitLossReport'])->name('export-profit-loss-report');

Route::get('/profit-loss-year-report',[AcctProfitLossYearReportController::class, 'index'])->name('profit-loss-year-report');
Route::post('/profit-loss-year-report/filter',[AcctProfitLossYearReportController::class, 'filterProfitLossYearReport'])->name('filter-profit-loss-year-report');
Route::get('/profit-loss-year-report/reset-filter',[AcctProfitLossYearReportController::class, 'resetFilterProfitLossYearReport'])->name('reset-filter-profit-loss-year-report');
Route::get('/profit-loss-year-report/print',[AcctProfitLossYearReportController::class, 'printProfitLossYearReport'])->name('print-profit-loss-year-report');
Route::get('/profit-loss-year-report/export',[AcctProfitLossYearReportController::class, 'exportProfitLossYearReport'])->name('export-profit-loss-year-report');

Route::get('/profit-day-report',[AcctProfitDayReportController::class, 'index'])->name('profit-day-report');
Route::post('/profit-day-report/filter',[AcctProfitDayReportController::class, 'filterProfitDayReport'])->name('filter-profit-day-report');
Route::get('/profit-day-report/reset-filter',[AcctProfitDayReportController::class, 'resetFilterProfitDayReport'])->name('reset-filter-profit-day-report');
Route::get('/profit-day-report/print',[AcctProfitDayReportController::class, 'printProfitDayReport'])->name('print-profit-day-report');
Route::get('/profit-day-report/export',[AcctProfitDayReportController::class, 'exportProfitDayReport'])->name('export-profit-day-report');

Route::get('/sales-customer',[SalesCustomerController::class, 'index'])->name('sales-customer');
Route::get('/sales-customer/add',[SalesCustomerController::class, 'addSalesCustomer'])->name('add-sales-customer');
Route::post('/sales-customer/process-add',[SalesCustomerController::class, 'processAddSalesCustomer'])->name('process-add-sales-customer');
Route::get('/sales-customer/edit/{customer_id}',[SalesCustomerController::class, 'editSalesCustomer'])->name('edit-sales-customer');
Route::post('/sales-customer/process-edit',[SalesCustomerController::class, 'processEditSalesCustomer'])->name('process-edit-sales-customer');
Route::get('/sales-customer/delete/{customer_id}',[SalesCustomerController::class, 'deleteSalesCustomer'])->name('delete-sales-customer');

Route::get('/cash-receipts-report', [AcctReceiptsReportController::class, 'index'])->name('cash-receipts-report');
Route::post('/cash-receipts-report/filter',[AcctReceiptsReportController::class, 'filterAcctReceiptsReport'])->name('fiter-cash-receipts-report');
Route::get('/cash-receipts-report/reset-filter',[AcctReceiptsReportController::class, 'resetFilterAcctReceiptsReport'])->name('reset-filter-cash-receipts-report');
Route::get('/cash-receipts-report/print',[AcctReceiptsReportController::class, 'printAcctReceiptsReport'])->name('print-cash-receipts-report');
Route::get('/cash-receipts-report/export',[AcctReceiptsReportController::class, 'exportAcctReceiptsReport'])->name('export-cash-receipts-report');

Route::get('/cash-disbursement-report',[AcctDisbursementReportController::class, 'index'])->name('cash-disbursement-report');
Route::post('/cash-disbursement-report/filter',[AcctDisbursementReportController::class, 'filterDisbursementReport'])->name('filter-cash-disbursement-report');
Route::get('/cash-disbursement-report/reset-filter',[AcctDisbursementReportController::class, 'resetFilterDisbursementReport'])->name('reset-filter-cash-disbursement-report');
Route::get('/cash-disbursement-report/print',[AcctDisbursementReportController::class, 'printDisbursementReport'])->name('print-cash-disbursement-report');
Route::get('/cash-disbursement-report/export',[AcctDisbursementReportController::class, 'exportDisbursementReport'])->name('export-cash-disbursement-report');
Route::get('/cash-disbursement-report/delete/{expenditure_id}',[AcctDisbursementReportController::class, 'deleteDisbursementReport'])->name('delete-cash-disbursement-report');

Route::get('/stock-out',[InvtStockOutController::class, 'index'])->name('stock-out');
Route::post('/stock-out/filter',[InvtStockOutController::class, 'filterStockOut'])->name('filter-stock-out');
Route::get('/stock-out/reset-filter',[InvtStockOutController::class, 'resetFilterStockOut'])->name('reset-filter-stock-out');
Route::get('/stock-out/add',[InvtStockOutController::class, 'addStockOut'])->name('add-stock-out');
Route::post('/stock-out/process-add',[InvtStockOutController::class, 'processAddStockOut'])->name('process-add-stock-out');
Route::post('/stock-out/add-array',[InvtStockOutController::class, 'addArrayStockOut'])->name('add-array-stock-out');
Route::get('/stock-out/add-delete-array/{record_id}',[InvtStockOutController::class, 'addDeleteArrayStockOut'])->name('add-delete-array-stock-out');
Route::post('/stock-out/add-elements',[InvtStockOutController::class, 'addElementsStockOut'])->name('add-elements-stock-out');
Route::get('/stock-out/add-delete-elements',[InvtStockOutController::class, 'addDeleteelEmentsStockOut'])->name('add-delete-elements-stock-out');
Route::get('/stock-out/detail/{stock_out_id}',[InvtStockOutController::class, 'detailStockOut'])->name('detail-stock-out');

Route::get('/stock-transfer',[InvtStockTransferController::class, 'index'])->name('stock-transfer');
Route::post('/stock-transfer/filter',[InvtStockTransferController::class, 'filterStockTransfer'])->name('filter-stock-transfer');
Route::get('/stock-transfer/reset-filter',[InvtStockTransferController::class, 'resetFilterStockTransfer'])->name('reset-filter-stock-transfer');
Route::get('/stock-transfer/add',[InvtStockTransferController::class, 'addStockTransfer'])->name('add-stock-transfer');
Route::post('/stock-transfer/process-add',[InvtStockTransferController::class, 'processAddStockTransfer'])->name('process-add-stock-transfer');
Route::post('/stock-transfer/add-array',[InvtStockTransferController::class, 'addArrayStockTransfer'])->name('add-array-stock-transfer');
Route::get('/stock-transfer/add-delete-array/{record_id}',[InvtStockTransferController::class, 'addDeleteArrayStockTransfer'])->name('add-delete-array-stock-transfer');
Route::post('/stock-transfer/add-elements',[InvtStockTransferController::class, 'addElementsStockTransfer'])->name('add-elements-stock-transfer');
Route::get('/stock-transfer/add-delete-elements',[InvtStockTransferController::class, 'addDeleteelEmentsStockTransfer'])->name('add-delete-elements-stock-transfer');
Route::get('/stock-transfer/detail/{stock_transfer_id}',[InvtStockTransferController::class, 'detailStockTransfer'])->name('detail-stock-transfer');
Route::get('/stock-transfer/print', [InvtStockTransferController::class, 'printStockTransfer'])->name('print-stock-transfer');
Route::get('/stock-transfer/export', [InvtStockTransferController::class, 'exportStockTransfer'])->name('export-stock-transfer');


Route::get('/system-ppn',[SystemPPNController::class, 'index'])->name('system-ppn');
Route::post('/system-ppn/process-edit',[SystemPPNController::class, 'processEditSystemPPN'])->name('process-edit-system-ppn');

Route::get('balance-sheet-report',[AcctBalanceSheetReportController::class, 'index'])->name('balance-sheet-report');
Route::post('balance-sheet-report/filter',[AcctBalanceSheetReportController::class, 'filterAcctBalanceSheetReport'])->name('filter-balance-sheet-report');
Route::get('balance-sheet-report/reset-filter',[AcctBalanceSheetReportController::class, 'resetFilterAcctBalanceSheetReport'])->name('reset-filter-balance-sheet-report');
Route::get('balance-sheet-report/print',[AcctBalanceSheetReportController::class, 'printAcctBalanceSheetReport'])->name('print-balance-sheet-report');
Route::get('balance-sheet-report/export',[AcctBalanceSheetReportController::class, 'exportAcctBalanceSheetReport'])->name('export-balance-sheet-report');

// data tabel
Route::get('/table-sales-invoice-by-item',[SalesInvoicebyItemReportController::class, 'tableSalesInvoiceByItem']);
Route::get('/table-purchase-item-report',[PurchaseInvoicebyItemReportController::class, 'tablePurchaseItemReport']);

Route::prefix('journal-categories')->middleware('auth')->group(function () {
    Route::get('/', [JournalCategoriesController::class, 'index'])->name('journal-categories.index');

    Route::get('/add', [JournalCategoriesController::class, 'addJournalCategories'])->name('journal-categories.add');
    Route::post('/process-add', [JournalCategoriesController::class, 'processAddJournalCategories'])->name('journal-categories.process-add');
    Route::get('/edit/{journal_categories_id}', [JournalCategoriesController::class, 'editJournalCategories'])->name('journal-categories.edit');
    Route::post('/process-edit', [JournalCategoriesController::class, 'processEditJournalCategori'])->name('journal-categories.process-edit');
    Route::get('/delete/{journal_categories_id}', [JournalCategoriesController::class, 'deleteJournalCategori'])->name('journal-categories.delete');
    
});

Route::prefix('journal-categories-formula')->group(function () {
    Route::get('/{journal_categories_id}', [JournalCategoriesFormulaController::class, 'index'])->name('journal-categories-formula.index');

    Route::get('/{journal_categories_id}/add', [JournalCategoriesFormulaController::class, 'addJournalCategoriesFormula'])->name('add');
    Route::post('/process-add', [JournalCategoriesFormulaController::class, 'processAddJournalCategoriesFormula'])->name('journal-categories-formula.process-add');
    Route::get('/edit/{journal_categories_id}', [JournalCategoriesFormulaController::class, 'editJournalCategoriesFormula'])->name('journal-categories-formula.edit');
    Route::post('/process-edit', [JournalCategoriesFormulaController::class, 'processEditJournalCategoriFormula'])->name('journal-categories-formula.process-edit');
    Route::get('/delete/{journal_categories_id}', [JournalCategoriesFormulaController::class, 'deleteJournalCategoriFormula'])->name('journal-categories-formula.delete');
    Route::post('/array', [JournalCategoriesFormulaController::class, 'addArrayJournalCategoriFormula'])->name('journal-categories-formula.array');
    Route::post('/elements', [JournalCategoriesFormulaController::class, 'addElementsJournalCategoriFormula'])->name('journal-categories-formula.elements');
    Route::get('/reset', [JournalCategoriesFormulaController::class, 'resetAddJournalCategoriFormula'])->name('journal-categories-formula.reset');

});


Route::get('/sales-collection', [SalesCollectionController::class, 'index'])->name('sales-collection');
Route::post('/sales-collection/filter', [SalesCollectionController::class, 'filterSalesCollection'])->name('filter-sales-collection');
// Route::get('/sales-collection/search', [SalesCollectionController::class, 'searchCoreCustomer'])->name('search-core-supplier-sales-collection');
Route::get('/sales-collection/search', [SalesCollectionController::class, 'searchInvoice'])->name('search-invoice-sales-collection');
Route::get('/sales-collection/add/{sales_invoice_id}', [SalesCollectionController::class, 'addSalesCollection'])->name('add-sales-collection');
Route::get('/sales-collection/detail/{sales_invoice_id}', [SalesCollectionController::class, 'detailSalesCollection'])->name('detail-sales-collection');
Route::get('/sales-collection/delete/{sales_invoice_id}', [SalesCollectionController::class, 'deleteSalesCollection'])->name('delete-sales-collection');
Route::post('/sales-collection/process-delete', [SalesCollectionController::class, 'processVoidSalesCollection'])->name('process-delete-sales-collection');
Route::post('/sales-collection/process-add/', [SalesCollectionController::class, 'processAddSalesCollection'])->name('process-add-sales-collection');
Route::post('/sales-collection/elements-add/', [SalesCollectionController::class, 'elements_add'])->name('elements-add-sales-collection');
Route::post('/sales-collection/add-bank/', [SalesCollectionController::class, 'addCoreBank'])->name('add-bank-sales-collection');
Route::post('/sales-collection/add-transfer-array/', [SalesCollectionController::class, 'processAddTransferArray'])->name('add-transfer-array-sales-collection');
Route::get('/sales-collection/delete-transfer-array/{record_id}/{sales_invoice_id}', [SalesCollectionController::class, 'deleteTransferArray'])->name('delete-transfer-array-sales-collection');
Route::get('/sales-collection/printing/{collection_id}', [SalesCollectionController::class, 'processPrintingSalesCollection'])->name('printing-sales-collection');
