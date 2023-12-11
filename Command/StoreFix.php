<?php
namespace Zero1\StoresCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Store\Api\StoreRepositoryInterface as StoreRepository;
use Magento\Framework\Exception\NoSuchEntityException;


class StoreFix extends Command
{
    public const STORE_ID = 'store-id';

    /** @var EventManager */
    protected $eventManager;

    /** @var StoreRepository */
    protected $storeRepository;

    public function __construct(
        EventManager $eventManager,
        StoreRepository $storeRepository  
    ){
        $this->eventManager = $eventManager;
        $this->storeRepository = $storeRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('store:store:fix');
        $this->setDescription('Fix a previously created store that may be missing sequence data.');
        $this->addOption(
            self::STORE_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Store ID of the store to fix'
        );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeId = $input->getOption(self::STORE_ID);

        if(!$storeId){
            $output->writeln('<error>Store ID missing</error>');
            return 1;
        }

        try{
            $store = $this->storeRepository->getById($storeId);
        }catch(NoSuchEntityException $e){
            $output->writeln('<error>Store with ID: '.$storeId.' doesn\'t exist</error>');
            return 2;
        }

        $this->eventManager->dispatch('store_add', ['store' => $store]);

        $output->writeln('<info>Store should be fixed</info>');
        return 0;
    }
} 