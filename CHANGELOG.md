# Changelog

## 2020-08-24 - Lando LEMP Configuration

Lando requires a custom `nginx.conf` in order to work in a LEMP stack. 

While implementing this a number of other Mautic configuration improvements were made. 

- Compared a copy of Drupal8 Lando Recipe `nginx.conf` and this gist for Mautic: https://gist.github.com/proffalken/ebfa9debc4eef929b0163d11a80af349
- `Nginx-lando.conf` has parity with our `symfony.conf` settings. In the future it would be better if both of these configs were maintained in the same file.
- Added `php.ini` which was sourced from `mautic-eb` open source project. This `php.ini` is not yet implemented in our production deployments. 
- Implemented a `config_override.php` to stream logs to `/dev/stdout` so we can pick them up via Docker Logs / Lando Logs.
- Added `rabbitmq` configuration to Lando services.
- Added `redis` configuration to Lando services.
- Set up `TRUSTED_PROXIES` as an environment variable, and Lando does not require a proxy.
- Remove `/mautic/vendor` and `/mautic/bin` from the git history
- Clean up Dockerfile-base to remove `--enable-gd-native-ttf` flag which is no longer necessary in PHP7.2.
- Remove `xdebug` from `Dockerfile-base`.
- Preparing for future programmatic composer builds of Mautic, copy the `parameters_local.php` to a `config/` folder. Eventually we will symlink this file or copy it at build time. 
- `reset_admin_password.sh` to Reset Admin Password for Local on lando build.
    - Lando shortcut: `lando reset-admin-pw`
- `scripts/sync_db_live_to_local.sh` to remotely download a copy of the MySQL Database.
    - Lando shortcut: `lando pull-live-db` 

### WIP Commits

- WIP: Added conditional check for Lando host domain DEV_HOST so that local development is done with dev mode turned on automatically. 

### Deployment Notes

- Requires `TRUSTED_PROXIES` to be defined in the environment conditionally on builds.

### New @ToDos

- [ ] Use one nginx.conf for both Lando local development and K8s deployments.
- [ ] Use `php.ini` in K8s Deployments
- [ ] Rebuild the Dockerfile-base with `from php7.2-base` instead of custom source. 
- [x] Lando Logs vs. Docker Logs - Determine if we should conditionally check for a LANDO environment before streaming to `/dev/stdout`
- [ ] Allow `reset-admin-pw` to take an argument.