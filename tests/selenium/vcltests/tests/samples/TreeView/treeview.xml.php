<?php
<object class="Unit1" name="Unit1" baseclass="page">
  <property name="Background"></property>
  <property name="Caption">Unit1</property>
  <property name="DocType">dtNone</property>
  <property name="Font">
    <property name="Align">taNone</property>
    <property name="Case"></property>
    <property name="Color"></property>
    <property name="Family">Verdana</property>
    <property name="LineHeight"></property>
    <property name="Size">10px</property>
    <property name="Style"></property>
    <property name="Variant"></property>
    <property name="Weight"></property>
  </property>
  <property name="Height">600</property>
  <property name="IsMaster">0</property>
  <property name="Layout">
    <property name="Cols">5</property>
    <property name="Rows">5</property>
    <property name="Type">ABS_XY_LAYOUT</property>
  </property>
  <property name="Name">Unit1</property>
  <property name="Width">800</property>
  <property name="OnAfterShow"></property>
  <property name="OnAfterShowFooter"></property>
  <property name="OnBeforeShow"></property>
  <property name="OnBeforeShowHeader"></property>
  <property name="OnCreate"></property>
  <property name="OnShow"></property>
  <property name="OnShowHeader"></property>
  <property name="OnStartBody"></property>
  <property name="OnTemplate"></property>
  <object class="TreeView" name="TreeView1" >
    <property name="Height">411</property>
    <property name="ImageList">ImageList1</property>
    <property name="Items"><![CDATA[a:4:{i:0;a:6:{s:7:&quot;Caption&quot;;s:6:&quot;Item 1&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:0:{}}i:1;a:6:{s:7:&quot;Caption&quot;;s:6:&quot;Item 2&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:2:{i:0;a:6:{s:7:&quot;Caption&quot;;s:12:&quot;Sub item 2-1&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:1:{i:0;a:6:{s:7:&quot;Caption&quot;;s:14:&quot;Sub item 2-1-1&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:0:{}}}}i:1;a:6:{s:7:&quot;Caption&quot;;s:12:&quot;Sub item 2-2&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:0:{}}}}i:2;a:6:{s:7:&quot;Caption&quot;;s:6:&quot;Item 3&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:0:{}}i:3;a:6:{s:7:&quot;Caption&quot;;s:6:&quot;Item 4&quot;;s:10:&quot;ImageIndex&quot;;s:1:&quot;0&quot;;s:13:&quot;SelectedIndex&quot;;s:1:&quot;0&quot;;s:10:&quot;StateIndex&quot;;s:2:&quot;-1&quot;;s:3:&quot;Tag&quot;;s:1:&quot;0&quot;;s:5:&quot;Items&quot;;a:0:{}}}]]></property>
    <property name="Left">30</property>
    <property name="Name">TreeView1</property>
    <property name="Top">29</property>
    <property name="Width">274</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnChangeSelected"></property>
    <property name="OnShow"></property>
    <property name="jsOnChangeSelected">TreeView1JSChangeSelected</property>
  </object>
  <object class="Edit" name="edtNodeName" >
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">21</property>
    <property name="Left">344</property>
    <property name="Name">edtNodeName</property>
    <property name="Text">Test node</property>
    <property name="Top">32</property>
    <property name="Width">121</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
    <property name="OnSubmit"></property>
  </object>
  <object class="Button" name="btnAddNode" >
    <property name="Caption">Add node</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnAddNode</property>
    <property name="Top">56</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnAddNodeClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="Button" name="btnSelect3rd" >
    <property name="Caption">Select 3rd node</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnSelect3rd</property>
    <property name="Top">136</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnSelect3rdClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="Button" name="btnRemoveLast" >
    <property name="Caption">Remove last node</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnRemoveLast</property>
    <property name="Top">184</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnRemoveLastClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="Button" name="btnAddToSelected" >
    <property name="Caption">Add to selected</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnAddToSelected</property>
    <property name="Top">88</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnAddToSelectedClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="Button" name="btnExpandAll" >
    <property name="Caption">Expand all</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnExpandAll</property>
    <property name="Top">232</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnExpandAllClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="Button" name="btnCollapsAll" >
    <property name="Caption">Collaps all</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">344</property>
    <property name="Name">btnCollapsAll</property>
    <property name="Top">264</property>
    <property name="Width">120</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">btnCollapsAllClick</property>
    <property name="OnShow"></property>
  </object>
  <object class="CheckBox" name="CheckBox1" >
    <property name="Caption">Show selected node with server round trip</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">21</property>
    <property name="Left">344</property>
    <property name="Name">CheckBox1</property>
    <property name="Top">320</property>
    <property name="Width">280</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">CheckBox1Click</property>
    <property name="OnShow"></property>
    <property name="OnSubmit"></property>
  </object>
  <object class="Label" name="lblSelectedNode2" >
    <property name="Caption">-</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight">bold</property>
    </property>
    <property name="Height">13</property>
    <property name="Left">344</property>
    <property name="Name">lblSelectedNode2</property>
    <property name="ParentFont">0</property>
    <property name="Top">344</property>
    <property name="Width">256</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
  </object>
  <object class="Label" name="lblSelectedNode1" >
    <property name="Caption">Selected node: -</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">13</property>
    <property name="Left">32</property>
    <property name="Name">lblSelectedNode1</property>
    <property name="Top">8</property>
    <property name="Width">272</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
  </object>
  <object class="ImageList" name="ImageList1" >
        <property name="Left">736</property>
        <property name="Top">16</property>
    <property name="Images"><![CDATA[a:2:{s:1:&quot;0&quot;;s:90:&quot;%VCL_HTTP_PATH%/qooxdoo/framework/resource/icon/VistaInspirate/16/actions/dialog-apply.png&quot;;s:1:&quot;1&quot;;s:83:&quot;%VCL_HTTP_PATH%/qooxdoo/framework/resource/icon/VistaInspirate/16/actions/go-up.png&quot;;}]]></property>
    <property name="Name">ImageList1</property>
  </object>
</object>
?>
