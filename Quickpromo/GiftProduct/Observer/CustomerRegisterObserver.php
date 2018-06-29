<?php
namespace Quickpromo\GiftProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Quickpromo\GiftProduct\Helper\Email;

/**
 * Observer for Send email
 *
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */

class CustomerRegisterObserver implements ObserverInterface
{
    private $helperEmail;
    
    public function __construct(
        Email $helperEmail
    ) {
        $this->helperEmail = $helperEmail;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getData('request');
        $data = $request->getPostValue();
        if(!empty($data['coupon_code']) && !empty($data['gift_sku'])){
        
            return $this->helperEmail->sendEmail($data['coupon_code'],$data['gift_sku']);
        } 
    }
}