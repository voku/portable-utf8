# AGENTS.md

## Setup

- Install project dependencies with `composer install` from the repository root.
- Install documentation generator dependencies with `composer install` from `build/`.

## Validation

- Run the test suite with `php vendor/bin/phpunit -c phpunit.xml`.

## Documentation

- `README.md` is generated. Do not edit the generated API section by hand.
- Regenerate it from the repository root with `php /home/runner/work/portable-utf8/portable-utf8/build/generate_docs.php`.
- The generator reads `/home/runner/work/portable-utf8/portable-utf8/build/docs/base.md` and `/home/runner/work/portable-utf8/portable-utf8/src/voku/helper/UTF8.php`.

## Notes

- Keep changes focused and minimal.
- When updating public UTF8 APIs, regenerate `README.md` if the API documentation changes.
