[![Latest Stable Version](https://poser.pugx.org/patchlevel/event-sourcing-dashboard-bundle/v)](https://packagist.org/packages/patchlevel/event-sourcing-dashboard-bundle)
[![License](https://poser.pugx.org/patchlevel/event-sourcing-dashboard-bundle/license)](https://packagist.org/packages/patchlevel/event-sourcing-dashboard-bundle)

# Event-Sourcing-Dashboard-Bundle

"A dashboard to inspect your events, time travel through your aggregates and manage your subscriptions."

## Features

* Browse the raw event [store](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/store) and filter by aggregate, id, stream or event
* [Inspect](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/inspection) a single aggregate: its events, serialized state, snapshot and a full state dump
* Time travel through an aggregate to see its state at any [playhead](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/inspection)
* List all registered [events](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/events) together with their listeners and subscribers
* View and control [subscriptions](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/subscriptions): boot, run, pause, reactivate, rebuild or remove
* [Customize](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest/events) how events are rendered with the `#[Inspect]` attribute
* and much more...

## Installation

```bash
composer require --dev patchlevel/event-sourcing-dashboard-bundle
```

## Documentation

* Latest [Docs](https://patchlevel.dev/docs/event-sourcing-dashboard-bundle/latest)
* Related [Blog](https://patchlevel.dev/blog)

## Screenshots

### Store

![Screenshot1](docs/screenshot1.png)

### Inspector

![Screenshot2](docs/screenshot2.png)

### Subscriptions

![Screenshot3](docs/screenshot3.png)

### Events

![Screenshot4](docs/screenshot4.png)

## Integration

* [event-sourcing](https://github.com/patchlevel/event-sourcing)
* [event-sourcing-bundle](https://github.com/patchlevel/event-sourcing-bundle)

## Contributing

We are open to contributions as long as they are in line with
our [BC-Policy](https://patchlevel.dev/backward-compatibility-promise).

In addition to the general policy, the following rules apply to this bundle:

* **Twig templates are not covered.** Symfony allows overriding the templates of a bundle, but our templates,
  their names, blocks and variables can change in any release without notice.
* **Controller internals are not covered.** The controller classes, their methods and signatures are considered
  internal. The routes they serve (route names and paths) are covered by the BC-Policy.

Also note that the `composer.lock` is always generated with the newest supported PHP version as this is the version our tools run in the CI.
