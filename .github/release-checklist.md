# Checklist for new release

1. change version in `src/Application.php`
2. change version and date in `CHANGELOG.md`
3. open a new milestone for the next release
4. move any issues/PRs which were milestoned for the current release, but didn't make it to the next milestone
5. close the milestone for this release
6. add new Git tag and push it
7. edit the [draft release on GitHub](https://github.com/php-parallel-lint/PHP-Parallel-Lint/releases) (which is automatically created by the workflow) with summary of important changes with respect to last version
8. to Github release add diff link, e.g.
    ```
    For the details you can have a look at the [diff](https://github.com/php-parallel-lint/PHP-Parallel-Lint/compare/v1.0.0...v1.1.0).
    ```
9. make public notification about the release on Twitter (X) and Mastodon
