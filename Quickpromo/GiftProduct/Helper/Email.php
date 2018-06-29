<?php
namespace Quickpromo\GiftProduct\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
/**
 * Email template Gift Promo Product
 *
 * @category   Quickpromo
 * @package    Quickpromo_GiftProduct
 * @author     Rao <raoafzal25@gmail.com>
 * @copyright  idealwebz.com
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
    }

    public function sendEmail($code,$sku)
    {
        try {
            // fetch product by Sku.
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productObject = $objectManager->get('Magento\Catalog\Model\Product');
            $product = $productObject->loadByAttribute('sku', $sku);
           

            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('rao'),
                'email' => $this->escaper->escapeHtml('raoafzal25@gmail.com'),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_giftpromo_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'FreeProduct'  =>  $product->getName(),
                ])
                ->setFrom($sender)
                ->addTo('customer@giftpromo.com')
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume(); 
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}