<?php
namespace Zero1\StoresCli\Model;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Store\Api\StoreRepositoryInterface as StoreRepository;
use Magento\Store\Api\GroupRepositoryInterface as StoreGroupRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\GroupFactory as StoreGroupFactory;
use Magento\Store\Model\Group as StoreGroup;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Store creation logic in single class, see vendor/magento/module-backend/Controller/Adminhtml/System/Store/Save.php
 * for original logic
 */
class StoreCreate
{
    /** @var StoreRepository */
    protected $storeRepository;

    /** @var StoreGroupRepository */
    protected $storeGroupRepository;

    /** @var StoreFactory */
    protected $storeFactory;

    /** @var \Magento\Framework\Registry */
    protected $coreRegistry;

    public function __construct(
        StoreRepository $storeRepository,
        StoreGroupRepository $storeGroupRepository,
        StoreFactory $storeFactory,
        Registry $registry
    ){
        $this->storeRepository = $storeRepository;
        $this->storeGroupRepository = $storeGroupRepository;
        $this->storeFactory = $storeFactory;
        $this->coreRegistry = $registry;
    }

    public function execute(
        array $storeData
    ){
        if(!isset($storeData['group_id'])){
            throw new \InvalidArgumentException('\'group_id\' must be present in the store data provided');
        }

        $storeGroup = $this->storeGroupRepository->get($storeData['group_id']);

        $store = $this->storeFactory->create();
        $store->setData($storeData);
        $store->setId(null);
        $store->setWebsiteId($storeGroup->getWebsiteId());
        if (!$store->isActive() && $store->isDefault()) {
            throw new LocalizedException(
                __('The default store cannot be disabled')
            );
        }

        $this->coreRegistry->register('store_type', 'store');
        $this->coreRegistry->register('store_action', 'add');
        $store->save();
        //$store = $this->storeRepository->save($store);

        return $store;
    }
} 