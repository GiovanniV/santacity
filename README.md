# City of Santa Ana
This is the website repo for The City of Santa Ana

## Build Intructions
To avoid tracking large folder we followed the [recommended practice][composer-vendor] and aren't tracking the vendor folder. Therefore, once you perform a `git pull` you will need
to immediately run `composer install` which will fetch all dependencies in the /vendor folder immediately.

If you are on a dev environment you may also need to run `drush pm-uninstall simplesamlphp_auth` to uninstall SimpleSAML. Note that this is still included in the
Git repo to facilitate pushing to production. If you unsure if this is needed you might run `drush pml` to see if it currently running in the project.

[composer-vendor]: https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md
