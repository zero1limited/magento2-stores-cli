<?php
namespace Zero1\StoresCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Zero1\StoresCli\Model\StoreCreate as StoreCreator;

class StoreCreate extends Command
{
    public const GROUP_ID = 'group-id';
    public const NAME = 'name';
    public const CODE = 'code';
    public const ACTIVE = 'active';
    public const SORT_ORDER = 'sort-order';
    public const IS_DEFAULT = 'default';

    /** @var StoreCreator */
    protected $storeCreator;

    public function __construct(
        StoreCreator $storeCreator
    ){
        $this->storeCreator = $storeCreator;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('store:store:create');
        $this->setDescription('Create a new store');

        $this->addOption(
            self::GROUP_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Store Group ID to put the store in.'
        );
        $this->addOption(
            self::NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Name of the new store.'
        );
        $this->addOption(
            self::CODE,
            null,
            InputOption::VALUE_REQUIRED,
            'store code of the new store.'
        );
        $this->addOption(
            self::ACTIVE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Active is the store is active. (default: true)',
            true
        );
        $this->addOption(
            self::SORT_ORDER,
            null,
            InputOption::VALUE_OPTIONAL,
            'Sort order of the new store.',
            null
        );
        $this->addOption(
            self::IS_DEFAULT,
            null,
            InputOption::VALUE_OPTIONAL,
            'Should this be the default store. (default: null)',
            null
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating store');
        
        $groupId = $input->getOption(self::GROUP_ID);
        $name = $input->getOption(self::NAME);
        $code = $input->getOption(self::CODE);
        $active = $input->getOption(self::ACTIVE);
        $sortOrder = $input->getOption(self::SORT_ORDER);
        $default = $input->getOption(self::IS_DEFAULT);

        $storeData = [
            'group_id' => $groupId,
            'name' => $name,
            'code' => $code,
            'is_active' => (int)$this->convertToBoolean($active),
            'sort_order' => $sortOrder,
            'is_default' => $this->convertToBoolean($default, false),
        ];
        try{
            $store = $this->storeCreator->execute($storeData);
        }catch(\Exception | \Throwable $e){
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return $e->getCode() > 0 ? $e->getCode() : 1;
        }
        

        $output->writeln(sprintf('<info>Store Created %s [%d]</info>', $store->getName(), $store->getId()));
        return 0;
    }

    protected function convertToBoolean($value, $treatEmtpyAsFalse = true)
    {
        if($value === 'true' || $value === '1' || $value === 1 || $value === true){
            return true;
        }
        if($value === 'false' || $value === '0' || $value === 0 || $value === false){
            return false;
        }
        
        if($treatEmtpyAsFalse && ($value === null || $value === '')){
            return false;
        }

        if(!$treatEmtpyAsFalse && ($value === null || $value === '')){
            return null;
        }

        return (bool)$value;
    }
} 