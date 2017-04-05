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
use Plenty\Modules\Item\Variation\Contracts;
use Plenty\Modules\Order\Shipping\Contracts as Shipping;
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
     * @param DateTime $date
     */
    public function Procedure(EventProceduresTriggered $eventTriggered, DateTime $date){


        /** @var Order $order */
        $Order = $eventTriggered->getOrder();


        // Log and check the Order that fired the Trigger
        $this->getLogger(__FUNCTION__ . " OrderID: $Order->id ")->info("EventProcedure is triggerd! ");


        $ZeitpunktConfig = $this->configRepository->get('ShippingreleaseByTime.AfterProcedureOrderStatus');



        $date->setDate(date("Y"), date("m"), date("d"));
        $date->setTime($ZeitpunktConfig, 00, 00);

        $FreigabeZeitpunkt = $date->getTimestamp();

        if(time() < $FreigabeZeitpunkt){

            // Update Order
            $this->orderRepositoryContract->updateOrder([
                'statusId'   => $this->configRepository->get('ShippingreleaseByTime.AfterProcedureOrderStatus'),
            ],
            $Order->id);
        }




    }



}