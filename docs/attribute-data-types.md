# Attribute Data Types #

Wanderluster has a rich collection of attribute types.  Using the right attribute type allows you to put validation constraints on the data which allows computers to better reason with the data.

The attribute type also determines the form input that is displayed to users who are editing Wanderluster online.  A text attribute would be rendered as a text box, but a choice attribute might be rendered as a checkbox collection.



| Type          | Description   | Rendered As   |  Constraints      | 
|---            |---            |---            |---                |
| Boolean       | True/False    | Checkbox      |   |
| Integer          | Integer data (1,2,3..)   | Number field       | Less than, Greater than  |
| Numeric          | Numeric data (3.4, -5.11)   | Number field       | Less than, Greater than  |
| Text          | Shorter text    | TextBox       | Less than 250 Characters  |
| Email          | Email    | Email field       | Must be valid email format  |
| Url          | Url    | Url field       | Must be valid url format  |
| File Size          | Size of file (ex 1.1mb)    | Read Only Text Field       | Max file size.  |
| Mime Type          | Mime Type of File    | Read Only Text Field       | Restricted Mime Types  |
| Coordinates          | Lat/Long of an entity    | Coordinate Picker       | Must be valid latitude and longitude  |
| Telephone          | Telephone    | Telephone field       | Must be valid telephone format  |
| WYSIWYG       | Longer text   | Rich text area  |Less than 25,000
| Choice        | When choosing one of many options  | Radio buttons or Dropdown     | Only one can be selected |
| MultiChoice        | When choosing one or more of many options  | Checkboxes or Multiselect     | One or more may be selected |
| Date        | Choosing a date  | Date Select     | Not Before, Not After |
| DateTime        | Choosing a date and a time  | Date+Time Select     | Not Before, Not After |
| Time        | Choosing a time  | Time Select     | Not Before, Not After |
| Entity        | Linking one entity to another  | Entity Select    | Restrict by Entity Type |
| Collection        | Representing multiple entities  | Entity Multi-Select    | Restrict by Entity Type |
| ImageFile        | Image used to represent entity  | File Upload    | Valid file, maximum size |