POC Extension Based Phpactor
============================

Rephpactor is a POC for an extension based version of Phpactor with embedded
composer.

It will initself be nothing more than a Symfony Console application which
allows you to install extensions.

```bash
Rephpactor

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help               Displays help for a command
  list               Lists commands
 extension
  extension:install  Install extension
  extension:update   Update extensions
  extension:search   Search available extensions
  extension:list     List installed extensions
```

```bash
$ ./bin/rephpactor extension:list
+--------------------------------------+-----------+--------------------------------------+
| Name                                 | Version   | Description                          |
+--------------------------------------+-----------+--------------------------------------+
| phpactor/language-server-extension   | 1.0.x-dev | LSP compatible language server       |
| phpactor/rpc-extension               | 1.0.x-dev | Phpactor's original RPC protocol     |
| phpactor/completion-extension        | 1.0.x-dev | Completion framework                 |
| phpactor/worse-reflection-extension  | 1.0.x-dev | Completors and other terrbile things |
+--------------------------------------+-----------+--------------------------------------+
```


