<?php
namespace ShippingreleaseByTime\Providers;

/********************************************************************
 * File:    VersandfreigabeServiceProvider.php
 * Author:  Thorsten Laing ( laing@web.de )
 * Date:    05.04.17
 *******************************************************************/

use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Plugin\Log\Loggable;
use ShippingreleaseByTime\Controllers\OrderUpdateEventProcedure;


class VersandfreigabeServiceProvider extends ServiceProvider
{
    use Loggable;


    public function register()
    {
        $this->getApplication()->bind(OrderUpdateEventProcedure::class);
    }


    /**
     * @param EventProceduresService $eventProceduresService
     * @see   ProcedureEntry::PROCEDURE_GROUP_ORDER
     */
    public function boot(EventProceduresService $eventProceduresService){


        $eventProceduresService->registerProcedure('ShippingreleaseByTime' , ProcedureEntry::PROCEDURE_GROUP_ORDER, [
            'de' => 'Versandfreigabe nach Uhrzeit',
            'en' => 'Shippingrelease by time'

        ], 'ShippingreleaseByTime\\Controllers\\OrderUpdateEventProcedure@Procedure');

    }
}
