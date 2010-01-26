WizardView mini-module for Kohana 3.x
Version 0.1
by Muhammad Lukman Nasaruddin
===============================

Requirements:

- Kohana 3.x
- Enable module in bootstrap.php (or simply copy the 2 files into your application folder)

How to use:

Prepare the views that will make up the form pages in the wizard. 
- DO NOT add <form></form> tag in the views.
- The views can access to $_POST values through array $post with keys equal to the name of input fields.

In your controller, instantiate the wizard using:

$wizard = new Wizard(array('view1','view2','view3'), 'Title', 'Subtitle');

Then, prepare the initial data array to populate in the form if necessary:

$initdata = array(
  'field1' => 'Value 1'
  'field2' => 'Value 2'
);

Execute the form processing method, passing the initial data array:

$result = $wizard->execute($initdata);

Now, do your custom processing using the values in $result array:

- $result['button'] will tell which button is clicked ('save', 'prev' or 'next') - empty string means it's showing the initial page (no button clicked yet)
- $result['post'] is the array with all form field values (union of $initdata and all form pages that have been gone through)
- $result['cur_step'] will tell you the index of the current page (starts with 0)
- $result['next_step'] will tell you the index of the next page to display

Before showing the next page, it's possible to modify the behavior:

- call $wizard->show_error($error_message, $page_index_to_show) to show error message on a specific page
- call $wizard->set_data($post) to change the $post array
- call $wizard->replace_view($view_name, $step) to replace the view on step $step with $view_name
- set $wizard->formaction to set custom form action attribute (default to '' so normally you don't have to touch this)

Finally show the wizard by calling $wizard->render() or simply pass it to another view to be shown in-line (Wizard is a subclass of View so it can go where a View can go)






