<?php
namespace Search\Model\Filter;

use ArrayIterator;
use Cake\Core\App;
use Cake\Utility\Inflector;
use InvalidArgumentException;
use Search\Manager;

/**
 * FilterCollection
 */
class FilterCollection implements FilterCollectionInterface
{
    use FilterMethodsTrait;

    /**
     * @var \Search\Model\Filter\Base[] List of filter objects
     */
    protected $_filters = [];

    /**
     * Search Manager
     *
     * @var \Search\Manager
     */
    protected $_manager;

    /**
     * Constructor
     *
     * @param \Search\Manager $manager Search Manager instance.
     */
    public function __construct(Manager $manager)
    {
        $this->_manager = $manager;

        $this->initialize();
    }

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Adds filter to the collection.
     *
     * @param string $name Filter name.
     * @param string $filter Filter class name in short form like "Search.Value" or FQCN.
     * @param array $options Filter options.
     * @return $this
     */
    public function add($name, $filter, array $options = [])
    {
        $this->_filters[$name] = $this->loadFilter($name, $filter, $options);

        return $this;
    }

    /**
     * Loads a search filter.
     *
     * @param string $name Filter name.
     * @param string $filter Filter class name in short form like "Search.Value" or FQCN.
     * @param array $options Filter options.
     * @return \Search\Model\Filter\Base
     * @throws \InvalidArgumentException When no filter was found.
     */
    public function loadFilter($name, $filter, array $options = [])
    {
        if (empty($options['className'])) {
            $class = Inflector::classify($filter);
        } else {
            $class = $options['className'];
            unset($options['className']);
        }

        $className = App::className($class, 'Model/Filter');
        if (!$className) {
            throw new InvalidArgumentException(sprintf('Search filter "%s" was not found.', $class));
        }

        return new $className($name, $this->_manager, $options);
    }

    /**
     * Checks if a filter is in the collection
     *
     * @param string $name Name of the filter
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_filters[$name]);
    }

    /**
     * Returns filter from the collection
     *
     * @param string $name Name of the filter
     * @return \Search\Model\Filter\Base|null
     */
    public function get($name)
    {
        if (!isset($this->_filters[$name])) {
            return null;
        }

        return $this->_filters[$name];
    }

    /**
     * Removes a filter by name
     *
     * @param string $name Name of the filter
     * @return $this
     */
    public function remove($name)
    {
        unset($this->_filters[$name]);

        return $this;
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_filters);
    }
}
