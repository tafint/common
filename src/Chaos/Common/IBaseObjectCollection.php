<?php namespace Chaos\Common;

/**
 * Interface IBaseObjectCollection
 * @author ntd1712
 */
interface IBaseObjectCollection extends IBaseObject
{
    /**
     * Add an item at the end of the collection
     *
     * @param   mixed $item The item to add
     * @return  $this
     */
    function add($item);
    /**
     * Alias of add()
     */
    function append($item);
    /**
     * Prepend an item onto the beginning of the collection
     *
     * @param   mixed $item The item to prepend
     * @return  $this
     */
    function prepend($item);
    /**
     * Clear the collection, removing all items
     *
     * @return  $this
     */
    function clear();
    /**
     * Check whether an item is contained in the collection
     *
     * @param   mixed $item The item to search for
     * @return  boolean TRUE if the collection contains the item, FALSE otherwise
     */
    function contains($item);
    /**
     * Check whether the collection contains an item with the specified key/index
     *
     * @param   string|int $key The key/index to check for
     * @return  boolean TRUE if the collection contains an item with the specified key/index, FALSE otherwise
     */
    public function containsKey($key);
    /**
     * Get the item of the collection at the current iterator position
     *
     * @return  mixed
     */
    function current();
    /**
     * Set the internal iterator to the first item in the collection and return this item
     *
     * @return  mixed
     */
    function first();
    /**
     * Set the internal iterator to the last item in the collection and return this item
     *
     * @return  mixed
     */
    function last();
    /**
     * Move the internal iterator position to the next item and return this item
     *
     * @return  mixed
     */
    function next();
    /**
     * Test for the existence of an item that satisfies the given predicate
     *
     * @param   \Closure $closure The predicate
     * @return  boolean TRUE if the predicate is TRUE for at least one item, FALSE otherwise
     */
    function exists(\Closure $closure);
    /**
     * Return all the items of this collection that satisfy the predicate.
     * The order of the items is preserved.
     *
     * @param   \Closure $closure The predicate used for filtering
     * @return  $this A collection with the results of the filter operation
     */
    function filter(\Closure $closure);
    /**
     * Test whether the given predicate holds for all items of this collection.
     *
     * @param   \Closure $closure The predicate
     * @return  boolean TRUE, if the predicate yields TRUE for all items, FALSE otherwise
     */
    function forAll(\Closure $closure);
    /**
     * Get the item at the specified key/index
     *
     * @param   string|int $key The key/index of the item to retrieve
     * @return  mixed
     */
    public function get($key);
    /**
     * Get a native PHP array representation of the collection
     *
     * @return  array
     */
    function getArrayCopy();
    /**
     * Get all keys/indices of the collection
     *
     * @return  array The keys/indices of the collection, in the order of the corresponding items in the collection
     */
    function getKeys();
    /**
     * Get all values of the collection
     *
     * @return  array The values of all items in the collection, in the order they appear in the collection
     */
    function getValues();
    /**
     * Get the index/key of a given item. The comparison of two items is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param   mixed $item The item to search for
     * @return  int|string|boolean The key/index of the item or FALSE if the item was not found
     */
    function indexOf($item);
    /**
     * Check whether the collection is empty (contains no items)
     *
     * @return  boolean TRUE if the collection is empty, FALSE otherwise
     */
    function isEmpty();
    /**
     * Get the key/index of the item at the current iterator position
     *
     * @return  int|string
     */
    function key();
    /**
     * Apply the given function to each item in the collection and returns
     * a new collection with the items returned by the function.
     *
     * @param   \Closure $closure
     * @return  $this A collection with the results of the map operation
     */
    function map(\Closure $closure);
    /**
     * Partition this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param   \Closure $closure The predicate on which to partition
     * @return  array An array with two items. The first item contains the collection
     *                of items where the predicate returned TRUE, the second item
     *                contains the collection of items where the predicate returned FALSE
     */
    function partition(\Closure $closure);
    /**
     * Remove the item at the specified index from the collection
     *
     * @param   string|int $offset The key/index of the item to remove
     * @return  mixed The removed item or NULL, if the collection did not contain the item
     */
    function remove($offset);
    /**
     * Remove the specified item from the collection, if it is found
     *
     * @param   mixed $item The item to remove
     * @return  boolean TRUE if this collection contained the specified item, FALSE otherwise
     */
    function removeItem($item);
    /**
     * Set an item in the collection at the specified key/index
     *
     * @param   string|int $key The key/index of the item to set
     * @param   mixed $value The item to set
     */
    function set($key, $value);
    /**
     * Extract a slice of $length items starting at position $offset from the collection.
     * If $length is null it returns all items from $offset to the end of the collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the items contained in the collection slice is called on.
     *
     * @param   int $offset The offset to start from
     * @param   int|null $length The maximum number of items to return, or null for no limit
     * @return  array
     */
    function slice($offset, $length = null);
}