# UTF8 mapping footprint (S0, S2, S6, S7)

Emoji are stored as ordered, readable UTF8 name/value rows; loading reconstructs the exact
original map before existing sorting/collision/cache logic runs. Byte maps are generated as
arrays, including ord's historical empty-string key. Raw `chr` replacement was deliberately
avoided because its wrapping behavior would erase the old array-index warning contract.
All original mapping paths and public signatures remain available on PHP >=7.1.

The support getter reserves the historical metadata key order without immediately loading
the list for an unrelated query. A full/list query still materializes the same values and
locale operations retain their existing lazy initialization. No installed-size saving is
claimed for support laziness.

```sh
php build/footprint/compact.php          # read-only deterministic check
php build/footprint/compact.php --write  # regenerate from current map values
php vendor/bin/phpunit -c phpunit.xml
```

`tests/fixtures/mapping-contract.json` pins all eight map arrays from
`28cb685c22cd829aba819406295c0763204c5c09`. Representation generation must retain these hashes.
A deliberate semantic emoji update needs separate fixture/test review.

The sibling `portable-utf8-benchmark/tools/footprint/` checkout contains the executable locked
production-consumer installs, full mapping/API matrix, ICU experiment, mandatory-package
prototype, diagnostic-aware differential tests, runtime samples, ADR and release notes.
The full UTF8 Dist dependency closure saves 235,496 logical and 86,016 allocated bytes;
file counts and dependency requirements do not change. Ordered emoji row parsing retains
~50 KB more runtime memory in the measured native profile; it is a footprint trade-off.

All 2,004 native tests and all eight intl/mbstring/iconv differential profiles pass on PHP8.4.
Public signatures match stable6.1.1; behavioral comparisons preserve current master.
PHPStan's six current errors are independently reproduced on untouched master. The legacy
formatter toolchain and unrun PHP7.1/7.4/8.5 jobs remain release-review limitations.
No mandatory mapping package, changed extension fallback, raised PHP floor, release tag or
publication. Because paths/API contracts are retained, no major migration is required here.
