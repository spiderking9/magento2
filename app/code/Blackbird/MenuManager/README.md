# Blackbird_MenuManager

Module provides powerful menu editor.

List of menus is located in Admin Panel under `Content > Elements > Menus`.

## Use

Following is an example of how to replace the main menu with a user defined menu (with identifier `menu-1`).

```
<referenceContainer name="main">
  <block name="menu" class="Blackbird\MenuManager\Block\Menu">
     <arguments>
        <argument name="menu" xsi:type="string">menu-1</argument>
     </arguments>
  </block>
</referenceContainer>

```
To render a menu with identifier "menu-1" on a page :

Copy the xml above and put it in Content > Page > (edit Page) > Design > Layout Update XML.
Make sure to write the right identifier for your menu on this line : 
```
<argument name="menu" xsi:type="string">menu-1</argument>
```

##Setting your own templates for your menu

Create a repository under Blackbird/MenuManager/view/frontend/templates/menu/view/{{The identifier of your menu}}/nodetype/{{Type of the node}}.phtml
Each type of node have its own template (.phtml file). Their names are category, cms_block, cms_page, content, customfield, group, product.
Before setting your own template you should take a look of the default ones in :
Blackbird/MenuManager/view/frontend/templates/menu/view/default/nodetype/

##Setting the menu for all your pages at once

To achieve that you need to have a custom theme and have a default.xml file.
In this xml file you need to have a code looking like that with in argument of the block the identifier of the menu you want on every pages :
```
<referenceContainer name="page.top">
            <block name="menu" class="Blackbird\MenuManager\Block\Menu">
                <arguments>
                    <argument name="menu" xsi:type="string">premier-menu-test</argument>
                </arguments>
            </block>
        </referenceContainer>
```
You can also change the referenceContainer as you want it to be.