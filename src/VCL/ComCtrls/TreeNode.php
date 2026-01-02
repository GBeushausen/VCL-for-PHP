<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\Core\Persistent;

/**
 * TreeNode represents a node in a TreeView control.
 *
 * Each TreeNode can contain child nodes, forming a tree structure.
 * Nodes have properties like Caption, ImageIndex, and can be expanded
 * or collapsed.
 *
 * PHP 8.4 version with Property Hooks.
 */
class TreeNode extends Persistent
{
    protected string $_caption = '';
    protected bool $_expanded = false;
    protected int $_imageindex = -1;
    protected string $_itemid = '';
    protected array $_items = [];
    protected int $_level = 0;
    protected int $_selectedindex = -1;
    protected mixed $_tag = 0;
    protected ?TreeNode $_parentnode = null;

    // Property Hooks
    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    public bool $Expanded {
        get => $this->_expanded;
        set => $this->_expanded = $value;
    }

    public int $ImageIndex {
        get => $this->_imageindex;
        set => $this->_imageindex = $value;
    }

    public string $ItemID {
        get => $this->_itemid;
        set => $this->_itemid = $value;
    }

    public array $Items {
        get => $this->_items;
        set => $this->_items = $value;
    }

    public int $Level {
        get => $this->_level;
        set => $this->_level = $value;
    }

    public int $SelectedIndex {
        get => $this->_selectedindex;
        set => $this->_selectedindex = $value;
    }

    public mixed $Tag {
        get => $this->_tag;
        set => $this->_tag = $value;
    }

    public ?TreeNode $ParentNode {
        get => $this->_parentnode;
        set => $this->_parentnode = $value;
    }

    public function __construct()
    {
        parent::__construct();
        // Get a unique ID for each tree node
        $this->_itemid = uniqid();
    }

    /**
     * Add a child node to this node.
     *
     * @param string $caption The caption of the new node.
     * @param mixed $tag The tag for custom identification.
     * @param int $imageindex Image list index for the node icon.
     * @param int $selectedindex Index of selected icon.
     * @return TreeNode Returns the newly created TreeNode.
     */
    public function addChild(
        string $caption,
        mixed $tag = 0,
        int $imageindex = -1,
        int $selectedindex = -1
    ): TreeNode {
        $node = new TreeNode();
        $node->ParentNode = $this;
        $node->Level = $this->_level + 1;
        $node->Caption = $caption;
        $node->Tag = $tag;
        $node->ImageIndex = $imageindex;
        $node->SelectedIndex = $selectedindex;

        $this->_items[] = $node;

        return $node;
    }

    /**
     * Find a TreeNode by its ItemID.
     *
     * Searches this node and all child nodes recursively.
     *
     * @param string $itemid ItemID to search for.
     * @return TreeNode|null Returns the TreeNode if found, null otherwise.
     */
    public function findNodeWithItemID(string $itemid): ?TreeNode
    {
        if ($this->_itemid === $itemid) {
            return $this;
        }

        if (count($this->_items) > 0) {
            foreach ($this->_items as $item) {
                $result = $item->findNodeWithItemID($itemid);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Count all nodes including this one and all descendants.
     */
    public function countAll(): int
    {
        $count = 1;
        foreach ($this->_items as $item) {
            $count += $item->countAll();
        }
        return $count;
    }

    /**
     * Get all leaf nodes (nodes without children).
     */
    public function getLeafNodes(): array
    {
        if (count($this->_items) === 0) {
            return [$this];
        }

        $leaves = [];
        foreach ($this->_items as $item) {
            $leaves = array_merge($leaves, $item->getLeafNodes());
        }
        return $leaves;
    }

    /**
     * Check if this node has children.
     */
    public function hasChildren(): bool
    {
        return count($this->_items) > 0;
    }

    /**
     * Get the root node of this tree.
     */
    public function getRoot(): TreeNode
    {
        if ($this->_parentnode === null) {
            return $this;
        }
        return $this->_parentnode->getRoot();
    }

    // Legacy getters/setters
    public function getCaption(): string { return $this->_caption; }
    public function setCaption(string $value): void { $this->Caption = $value; }
    public function defaultCaption(): string { return ''; }

    public function getExpanded(): bool { return $this->_expanded; }
    public function setExpanded(bool $value): void { $this->Expanded = $value; }
    public function defaultExpanded(): int { return 0; }

    public function getImageIndex(): int { return $this->_imageindex; }
    public function setImageIndex(int $value): void { $this->ImageIndex = $value; }
    public function defaultImageIndex(): int { return -1; }

    public function getItemID(): string { return $this->_itemid; }
    public function setItemID(string $value): void { $this->ItemID = $value; }

    public function getItems(): array { return $this->_items; }
    public function setItems(array $value): void { $this->Items = $value; }

    public function getLevel(): int { return $this->_level; }
    public function setLevel(int $value): void { $this->Level = $value; }
    public function defaultLevel(): int { return 0; }

    public function getSelectedIndex(): int { return $this->_selectedindex; }
    public function setSelectedIndex(int $value): void { $this->SelectedIndex = $value; }
    public function defaultSelectedIndex(): int { return -1; }

    public function getTag(): mixed { return $this->_tag; }
    public function setTag(mixed $value): void { $this->Tag = $value; }
    public function defaultTag(): int { return 0; }

    public function getParentNode(): ?TreeNode { return $this->_parentnode; }
    public function setParentNode(?TreeNode $value): void { $this->ParentNode = $value; }
}
