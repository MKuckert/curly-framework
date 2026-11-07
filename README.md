# Curly Framework v1 (archived)

> **Archived, unmaintained.** This is a snapshot of my personal PHP framework from
> **2007–2009**, written for **PHP 5.2** on top of **Zend Framework 1**. It is
> published for historical/reference purposes only and will not run on modern PHP
> without changes. Successor projects: [curly-streams](https://github.com/MKuckert/curly-streams)
> (Curly V2), [curly-canvas](https://github.com/MKuckert/curly-canvas),
> [pipc](https://github.com/MKuckert/pipc).

Curly was a library of extensions to Zend Framework 1. It also contained the
small ZF1 applications that powered *curlybracket.de* at the time. Version 1.0
was released on 17.06.2009 (see [`library/CHANGELOG.txt`](library/CHANGELOG.txt)).
Comments and docs are mostly in German.

## Layout

| Path | Description |
|---|---|
| `bootstrap.php`, `baseconfig.yml`, `*.config.yml`, `INCLUDEPATH`, `htdocs/` | Bootstrapping: reads include paths and YAML configs, then dispatches to a ZF1 application via `Curly_Bootstrap` |
| `library/Curly/` | **The framework (v1.0)**: class loaders (namespace, include path, recursive, dispatcher), bootstrap sections, event dispatcher, callbacks/delegates, typed and serial collections, `BigDecimal`, `String`, `Ensure` (type checks), hashes, streams and stream wrappers (string, Base64, GZ), YAML config, ACL (DB-backed), asset manager and view helpers, expression evaluator, HTTP status codes, `FrameworkBundler` (packs selected ZF1 components), source code tools (PHP tokenizer, PHP/CSS/JS minimizers, a PHP preprocessor with `#define`/`#ifdef`/`#include`, a DocComment parser) |
| `library/CurlyLight/` | Reduced copy of `Curly` plus parts of `CurlyX` |
| `library/Laboratory/CurlyX/` | Experimental packages: archive format (compiler/parser with plain, compress and crypt chains), installer with manifest/XSD, extended PDO (`EPDO`) and DB drivers, module system, XML parser, PHP source formatter, YAML |
| `library/CurlyBracketDe/` | Site-specific controller, DB and page classes for curlybracket.de |
| `applications/curlybracketde/` | ZF1 application for the curlybracket.de website (content pages, build page) |
| `applications/bundler/` | ZF1 web UI for `Curly_FrameworkBundler` |
| `Test/` | PHPUnit 3 tests for `Curly` and `CurlyX` with fixtures |
| `figlet/` | FIGlet fonts (used for ASCII-art headers) |
| `locale/` | gettext catalog |
| `tmp/` | Scratch space: early prototypes (`Archive`, `Easypack_Tokens`, stream wrapper templates) and `tmp/review/`, my even older pre-Curly code from 2007/2008 (class loaders, code generation, image/canvas classes, DOM controls, HTTP client, XMI→PHP, ArgoUML models) |

## Requirements (historical)

- PHP 5.2
- Zend Framework 1.x (not included), and for some tests Doctrine 1.0
- Source files are encoded in **ISO-8859-1** with CRLF line endings

About 24 of 511 PHP files no longer parse on PHP 8, mostly because of `$str{$i}`
string offsets and other removed syntax.

## Removed for publication

- The production config (`curlybracketde.config.yml.curlybracketde`, which held
  database credentials) and the production `INCLUDEPATH.curlybracketde`
- Empty log files and Eclipse/PDT project metadata

## Third-party code

These files are included under their own licenses:

- `library/Curly/Yaml/Parser.php`, `Inline.php`: from the symfony YAML component, © Fabien Potencier, MIT License
- `library/*/Curly/Sourcecode/Minimizer/JavaScript.php`: JSMin PHP port, © 2002 Douglas Crockford, © 2008 Ryan Grove, MIT License
- `tmp/review/_temporary/ZipArchive.class.php`: based on PclZip 2.5 by Vincent Blavet, GNU LGPL
- `figlet/*.flf`: FIGlet fonts by their respective authors (see the font headers)

## License

Everything else: New BSD License, © 2007–2009 Martin Kuckert. See [LICENSE](LICENSE).
