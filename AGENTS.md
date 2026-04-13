# AGENTS.md

## Setup

- Install project dependencies with `composer install` from the repository root.
- Install documentation generator dependencies with `composer install` from `build/`.

## Validation

- Run the test suite with `php vendor/bin/phpunit -c phpunit.xml`.

## Documentation

- `README.md` is generated. Do not edit the generated API section by hand.
- Regenerate it from the repository root with `php build/generate_docs.php`.
- The generator reads `build/docs/base.md` and `src/voku/helper/UTF8.php`.

## Notes

- Keep changes focused and minimal.
- When updating public UTF8 APIs, regenerate `README.md` if the API documentation changes.
