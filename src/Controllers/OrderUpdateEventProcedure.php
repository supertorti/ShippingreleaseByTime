<?PHP
namespace ShippingreleaseByTime\Controllers;

/********************************************************************
 * File:    OrderUpdateEventProcedure.php
 * Author:  Thorsten Laing ( laing@web.de )
 * Date:    05.04.17
 *******************************************************************/


use Plenty\Modules\Order\Contracts as Order;
use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Modules\Comment\Contracts\CommentRepositoryContract;
use Plenty\Plugin\ConfigRepository;


class OrderUpdateEventProcedure {

    use Loggable;

    /**
     * @var Order\OrderRepositoryContract $orderRepositoryContract
     */
    protected $orderRepositoryContract;


    /**
     * @var CommentRepositoryContract $commentRepositoryContract
     */
    protected $commentRepositoryContract;


    /**
     * @var ConfigRepository $configRepository
     */
    protected $configRepository;

    /**
     * Helper to convert German time to English
     * @var $TimeCoonvert
     */
    protected $TimeConvert = [ 8 => "8am", 9 => "9am", 10 => "10am", 11 => "11am", 12 => "12am", 13 => "1pm", 14 => "2pm", 15 => "3pm", 16 => "4pm", 17 => "5pm", 18 => "6pm", 19 => "7pm", 20 => "8pm" ];


    /**
     * OrderUpdateEventProcedure constructor.
     * @param Order\OrderRepositoryContract $orderRepositoryContract
     * @param CommentRepositoryContract $commentRepositoryContract
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        Order\OrderRepositoryContract $orderRepositoryContract,
        CommentRepositoryContract $commentRepositoryContract,
        ConfigRepository $configRepository

    ){
        $this->orderRepositoryContract     = $orderRepositoryContract;
        $this->commentRepositoryContract   = $commentRepositoryContract;
        $this->configRepository            = $configRepository;
    }


    /**
     * @param EventProceduresTriggered $eventTriggered
     */
    public function Procedure(EventProceduresTriggered $eventTriggered){

        // Get the order
        $Order = $eventTriggered->getOrder();

        // Log the Order that fired the Trigger
        $this->getLogger(__FUNCTION__ . " OrderID: $Order->id ")->info("EventProcedure is triggerd! ");

        // Get && convert Config->releasetime
        $FreigabeZeitpunkt = $this->TimeConvert[ $this->configRepository->get('ShippingreleaseByTime.releasetime') ];


        // Do the stuff
        if(time() < strtotime("today $FreigabeZeitpunkt")){

            // Update Order
            $this->orderRepositoryContract->updateOrder([
                'statusId'   => $this->configRepository->get('ShippingreleaseByTime.AfterProcedureOrderStatus'),
            ],
            $Order->id);

            // Set comment
            $this->commentRepositoryContract->createComment([
                'referenceType'  => 'order',
                'userId'         => $this->configRepository->get('ShippingreleaseByTime.UserID'),
                'referenceValue' => $Order->id,
                'text'           => "Auftrag automatisch zum Tagesversand Freigegeben!",
                'isVisibleForContact' => false
            ]);
        }

    }



}