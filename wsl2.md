## install wsl
- `wsl --install`
- `wsl --update`
- https://learn.microsoft.com/en-us/windows/wsl/setup/environment
- `sudo apt update && sudo apt full-upgrade -y && sudo apt autoremove -y && - sudo apt-get dist-upgrade -y`
- `sudo apt install -y vim git curl ca-certificates`

## configure git
- https://learn.microsoft.com/en-us/windows/wsl/tutorials/wsl-git#git-config-file-setup
- `git config --global core.editor "vi"`

## install oh-my-bash
https://github.com/ohmybash/oh-my-bash

## install nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/master/install.sh | bash

## install docker-engine-ce
- https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository
- https://docs.docker.com/engine/install/linux-postinstall/
- `wsl --shutdown und neuen Tab starten`
- `docker run --rm hello-world`
- https://docs.ddev.com/en/stable/users/install/docker-installation/#testing-and-troubleshooting-your-docker-installation

## lando setup
- https://github.com/florianPat/lando-cli-setup

## IDE integration
- https://docs.ddev.com/en/stable/users/install/phpstorm/
- https://docs.ddev.com/en/stable/users/debugging-profiling/step-debugging/#troubleshooting-xdebug
- VS Code: WSL & php debug extensions
- @see: .vscode/launch.json for debug connection

## DNS rebind protection
- https://docs.lando.dev/help/dns-rebind.html, https://docs.ddev.com/en/stable/users/usage/troubleshooting/#ddev-starts-but-browser-cant-access-url
- `wsl-sudo ddev-hostname.exe demo.lndo.site 127.0.0.1`
- ddev-hostname: https://github.com/ddev/ddev/releases
- wsl-sudo: https://github.com/Chronial/wsl-sudo

## Notes:
#!/usr/bin/env bash
export PATH=$PATH:/usr/sbin:/sbin
phpdismod xdebug
killall -USR2 php-fpm 2>/dev/null || true
echo "Disabled xdebug"

#!/usr/bin/env bash
export PATH=$PATH:/usr/sbin:/sbin
phpenmod xdebug
killall -USR2 php-fpm 2>/dev/null || true
echo "Enabled xdebug"
