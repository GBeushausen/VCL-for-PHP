<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ComCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ComCtrls\TreeNode;

class TreeNodeTest extends TestCase
{
    private TreeNode $node;

    protected function setUp(): void
    {
        $this->node = new TreeNode();
    }

    public function testCaptionProperty(): void
    {
        $this->node->Caption = 'Root Node';
        $this->assertSame('Root Node', $this->node->Caption);
    }

    public function testExpandedProperty(): void
    {
        $this->node->Expanded = true;
        $this->assertTrue($this->node->Expanded);
    }

    public function testDefaultExpandedIsFalse(): void
    {
        $this->assertFalse($this->node->Expanded);
    }

    public function testImageIndexProperty(): void
    {
        $this->node->ImageIndex = 5;
        $this->assertSame(5, $this->node->ImageIndex);
    }

    public function testDefaultImageIndex(): void
    {
        $this->assertSame(-1, $this->node->ImageIndex);
    }

    public function testItemIDIsUnique(): void
    {
        $node2 = new TreeNode();
        $this->assertNotSame($this->node->ItemID, $node2->ItemID);
    }

    public function testLevelProperty(): void
    {
        $this->node->Level = 2;
        $this->assertSame(2, $this->node->Level);
    }

    public function testTagProperty(): void
    {
        $this->node->Tag = 'custom_data';
        $this->assertSame('custom_data', $this->node->Tag);
    }

    public function testAddChild(): void
    {
        $child = $this->node->addChild('Child Node', 'tag1', 1, 2);
        $this->assertInstanceOf(TreeNode::class, $child);
        $this->assertSame('Child Node', $child->Caption);
        $this->assertSame($this->node, $child->ParentNode);
        $this->assertSame(1, $child->Level);
    }

    public function testHasChildren(): void
    {
        $this->assertFalse($this->node->hasChildren());
        $this->node->addChild('Child');
        $this->assertTrue($this->node->hasChildren());
    }

    public function testCountAll(): void
    {
        $this->assertSame(1, $this->node->countAll());
        $child = $this->node->addChild('Child');
        $this->assertSame(2, $this->node->countAll());
        $child->addChild('Grandchild');
        $this->assertSame(3, $this->node->countAll());
    }

    public function testFindNodeWithItemID(): void
    {
        $child = $this->node->addChild('Child');
        $grandchild = $child->addChild('Grandchild');

        $found = $this->node->findNodeWithItemID($grandchild->ItemID);
        $this->assertSame($grandchild, $found);
    }

    public function testFindNodeWithItemIDReturnsNullForUnknown(): void
    {
        $this->assertNull($this->node->findNodeWithItemID('nonexistent'));
    }

    public function testGetRoot(): void
    {
        $child = $this->node->addChild('Child');
        $grandchild = $child->addChild('Grandchild');

        $this->assertSame($this->node, $grandchild->getRoot());
    }

    public function testGetLeafNodes(): void
    {
        $child1 = $this->node->addChild('Child1');
        $child2 = $this->node->addChild('Child2');
        $grandchild = $child1->addChild('Grandchild');

        $leaves = $this->node->getLeafNodes();
        $this->assertCount(2, $leaves);
        $this->assertContains($grandchild, $leaves);
        $this->assertContains($child2, $leaves);
    }

    public function testIsPersistent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Persistent::class, $this->node);
    }
}
