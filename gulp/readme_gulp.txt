1. Install Gulp:
If you've previously installed gulp globally, run npm rm --global gulp before following these instructions.
Install the gulp command

npm install --global gulp-cli


2. Install gulp and packages in src folder
Run this command in directory with files gulpfile.js and package.json (project_root_dir\gulp):

npm install


3. In gulpfile.js assign your localhost url to variable "host". IMPORTANT: Do not commit this change!


4. Execute gulp tasks in console, few options:

- gulp - run default task with watchers -- recommended --
- gulp <task_name> - to run specific task e.g. gulp sass


#######################
Additional Info
#######################

1. Install new modules with flag: --save-dev. Only to development usage
E.g.: npm install gulp-autoprefixer --save-dev
