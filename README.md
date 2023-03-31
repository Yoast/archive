# Yoast Repository Archive

This repository is used as a public archive of abandoned Yoast projects.

To add more repositories to the archive, simply do the following:

```SH
git subtree add --prefix={{directory}} https://github.com/yoast/{{repository}}.git master
```

Or to add a directory from another repository:

```SH
# Link and fetch the repository you want the directory from
git remote add project https://github.com/yoast/{{repository}}.git
git fetch project

# Split the directory, including history, to a temporary branch
git branch project_main project/main
git checkout -f project_main
git subtree split --prefix=path_of_interest_in_project -b temp_branch

# Add the temporary branch to the archive in a specific directory
git checkout -f main
git subtree add --prefix={{directory}} temp_branch
git push
```
