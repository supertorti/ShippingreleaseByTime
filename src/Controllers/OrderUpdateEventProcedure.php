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
use DateTime;


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
     * @var DateTime $dateTime
     */
    protected $dateTime;


    /**
     * OrderUpdateEventProcedure constructor.
     * @param Order\OrderRepositoryContract $orderRepositoryContract
     * @param CommentRepositoryContract $commentRepositoryContract
     * @param ConfigRepository $configRepository
     * @param DateTime $dateTime
     */
    public function __construct(
        Order\OrderRepositoryContract $orderRepositoryContract,
        CommentRepositoryContract $commentRepositoryContract,
        ConfigRepository $configRepository,
        DateTime $dateTime
    ){

        $this->orderRepositoryContract     = $orderRepositoryContract;
        $this->commentRepositoryContract   = $commentRepositoryContract;
        $this->configRepository            = $configRepository;
        $this->dateTime                    = $dateTime;
    }


    /**
     * @param EventProceduresTriggered $eventTriggered
     * @param DateTime $dateTime
     */
    public function Procedure(EventProceduresTriggered $eventTriggered){


        /** @var Order $order */
        $Order = $eventTriggered->getOrder();


        // Log and check the Order that fired the Trigger
        $this->getLogger(__FUNCTION__ . " OrderID: $Order->id ")->info("EventProcedure is triggerd! ");


        //$ZeitpunktConfig = $this->configRepository->get('ShippingreleaseByTime.releasetime');

/*
        $dateTime->setDate(date("Y"), date("m"), date("d"));
        $dateTime->setTime(15, 00, 00);

        $FreigabeZeitpunkt = $dateTime->getTimestamp();
*/
        if(TRUE){ //time() < $FreigabeZeitpunkt){

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
                'text'           => "Auftrag zum Versand Freigegeben!",
                'isVisibleForContact' => false
            ]);
        }

        //$this->getLogger(__FUNCTION__ . " OrderID: $Order->id ")->info("Debug:" . $FreigabeZeitpunkt);


    }



}