[[ $- != *i* ]] && return

alias te="tail /dat/local/logs/error.log -f"
alias console="/dat/entourage/app/common/lib/symfony/app/console"

export PS1="\[\033[00;32m\]\h \[\033[00m\]\w \\$ "
export PATH=/usr/local/bin
export PATH=$PATH:/bin:/usr/bin:/sbin:/usr/sbin:/opt/bin
export PATH=$PATH:/dat/bin:/dat/player/bin
export PATH=$PATH:/etc/init.d/

export EDITOR=/usr/bin/vim

#php
export LD_LIBRARY_PATH=/usr/lib/php5/20090626:/usr/local/lib
export BUILD_TYPE=dev

# Common Alias
alias ls="ls -aF --color=auto"
alias mv="mv -iv"
alias cp="cp -iv"
alias rm="rm -iv"
alias ..="cd .."
alias vi="vim"
alias g="grep . -R"
alias tl="tail -f /dat/local/logs/error.log"

