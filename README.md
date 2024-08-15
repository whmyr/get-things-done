# Get Things Done - Task Management

## Description
This extension provides simple task management features for frontend users
based on Extbase and Fluid and the Twitter Bootstrap Framework.

The basic templates provided are tested with the help of the
TYPO3 introduction package.

#### Task owners can:
- create tasks
- edit tasks
- remove tasks
- assign tasks to other frontend users
- mark tasks as done and undone

#### Task assignees can
- mark tasks as done and undone

## Installation
Install the extension via composer
```
composer require whmyr/get-things-done
```

## Configuration
1. Include the static typoscript provided by the extension inside of your root
   typoscript template
2. Configure the constants in the "get things done" category to set the relevant
   storage page ids and configure the pagination as needed
3. On the desired page, add a new "Insert Plugin" content element and choose the
   plugin "Task List"
4. (optional) The plugin content is completely restricted to logged in frontend
   users so it might be a good idea to set access restrictions to the content
   element itself to use the TYPO3 default behaviour to guide unauthorized users
   to the login form
