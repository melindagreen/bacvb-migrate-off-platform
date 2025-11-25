# Partner Portal
DMO Partners directly manage their Listings and Events 
- Project Specific - meta fields are easily configured from the theme
- Admin can review at a glance all listings that have been added or edited

## How to Use
1. If you are managing your plugins via composer in Madre/Nino, add the following to your project's root composer.json:

            "repositories": [{
                  "type": "vcs",
                  "url": "git@github.com:maddenmedia/partner-portal.git"
                }
            ],
            "require": {
                "maddenmedia/partner-portal": "*"    
            }

2. Copy the 'partnerportal-theme-files' directory from the root of the Partner Portal Plugin directory and paste it in the root of the active theme directory.

3. Activate your plugin and you should have a Listings Post Type now available
## Create your meta fields
In your theme's partnerportal-theme-files directory is a file called partner.json. That contains your meta field definitions. A typical meta-field definition looks like this:

    {
      "id" : "partner_hours_box",
      "title" : "Partner Hours",
      "inputs" : [
        {
          "key" : "hours_description",
          "label" : "Hours Description", 
          "icon" : "fas fa-at",
          "cssClass" : "regular-text",
          "newLineAfter" : false ,
          "type" : "textarea"
        }
    }
    
A "Gallery" field is set like so:

    {
      "type" : "gallery",
      "max_images": 1,
      "instructions" : "",
      "key" : "gallery_square_featured_image", 
      "label" : "Square Featured Image", 
      "cssClass" : "regular-text ",
      "newLineAfter" : true
    }

Add text between fields with:

    {
      "type" : "message",
      "content" : "</br></br>"
    }
    
Add a "Time" input field like so:

    {
      "key" : "closed_friday", 
      "label" : "Closed Friday  &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;", 
      "type": "time",
      "icon" : "fa fa-clock"
    }

Add a select like so:

    {
      "key" : "active_listing", 
      "label" : "Active", 
      "type" : "select",
      "newLineAfter" : false,        
      "options" : {
        "active":"Active",
        "inactive":"Inactive"
      },
      "defaultChoice" : "active"
    }       

Add checkboxes like so:

    {
      "key" : "lodging_amenities", 
      "label" : "<b>Amenities:</b>", 
      "type" : "checkbox",
      "newLineAfter" : true,        
      "choices" : [
        {
          "key":"kitchen",
          "label":"Kitchen"
        },
        {
          "key":"refrigerators-microwaves",
          "label":"Refrigerators/Microwaves"
        },
        {
          "key":"bike",
          "label":"Bike Storage"
        }
      ]
    }    
  
## Currently Used On:
[VisitHermann](https://visithermann.com/)
[VisitNatchez](https://visitnatchez.org/)
[DiscoverSanAngelo](http://discoversanangelo.com/)

## PartnerPortal has csv import and export options that have not been documented yet  

## To Do
- add config option to disable integration with eventastic 
