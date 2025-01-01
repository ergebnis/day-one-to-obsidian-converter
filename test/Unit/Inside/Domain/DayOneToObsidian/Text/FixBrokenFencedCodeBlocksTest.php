<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\DayOneToObsidian\Text\FixBrokenFencedCodeBlocks::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class FixBrokenFencedCodeBlocksTest extends Framework\TestCase
{
    public function testProcessFixesBrokenDayOneCodeBlocks(): void
    {
        $text = Inside\Domain\Shared\Text::fromString(
            <<<'MARKDOWN'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam tristique ipsum vitae tellus pharetra tempor. Suspendisse sollicitudin ex eu sapien fermentum, nec lacinia enim euismod. Fusce ac neque porta velit egestas mollis in ac justo. Morbi porttitor leo risus, ut accumsan tellus fringilla eu. Pellentesque ex ipsum, aliquet vel orci ut, tincidunt elementum erat. Sed nec leo massa. Curabitur massa felis, mattis ac nulla vulputate, mollis maximus dui. Pellentesque ac varius neque, eget placerat velit. Morbi eget ipsum odio. Sed eget arcu metus. Morbi pharetra sodales elit sed vehicula. Praesent efficitur velit ac fringilla accumsan. Vivamus scelerisque sem suscipit, pulvinar elit et, malesuada nunc.

```php
<?php

declare(strict_types=1);

final class Foo
{
    private Bar $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
```

Etiam consequat imperdiet magna in congue. Nunc laoreet tortor eget nunc bibendum, vitae finibus eros convallis. Nam molestie risus eu sem accumsan convallis. Ut fermentum nunc vel hendrerit tempor. Vestibulum ex est, iaculis ut urna et, fermentum scelerisque neque. Integer condimentum enim dolor, a lacinia eros ullamcorper sed. Donec dictum lacus nisl, ac imperdiet sapien porta sit amet. Donec congue felis hendrerit, pretium nibh sit amet, sodales diam. Morbi eros urna, pretium ut ante venenatis, ullamcorper dictum eros. Suspendisse quis molestie erat. Nullam ut lorem tristique, commodo purus ac, viverra justo. Nunc sed ullamcorper tellus, id ornare metus. Aenean pharetra, odio sed facilisis efficitur, enim nulla fringilla risus, consequat hendrerit eros sem eu ipsum. In mattis fermentum ultricies. Pellentesque ac orci in urna fringilla rhoncus non sed felis. Aliquam nec diam sit amet enim porta tempor.

```
final class Command
```

```
{
```

    ```
private $logger;
```


```

```
    ```
public function __construct(LoggerInterface $logger = null)
```

    ```
{
```

        ```
$this->logger = $logger;
```

    ```
}
```


```

```
    ```
public function execute()
```

    ```
{
```

        ```
if (null !== $this->logger) {
```

            ```
$this->logger->info();
```

        ```
}
```

    ```
}
```

```
}
```

Praesent varius est vel elit aliquam, eu consequat nisi feugiat. Nulla facilisi. Fusce posuere tincidunt leo ac facilisis. Vestibulum ex mi, faucibus at feugiat vel, volutpat non risus. Quisque ipsum mauris, rhoncus non purus non, luctus auctor leo. Sed facilisis lobortis leo, eu consequat mi tempus nec. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam convallis at odio non sodales. In non tempor sapien. Maecenas est arcu, venenatis sed interdum vitae, accumsan eu magna. Fusce volutpat odio a nisi porta consequat. Duis ipsum ex, laoreet eget condimentum sed, hendrerit mattis massa. Nunc nec viverra nisl. Fusce at orci a ligula ornare vestibulum. Vestibulum luctus tortor vitae mauris rhoncus malesuada. Praesent molestie auctor viverra.

MARKDOWN,
        );

        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\FixBrokenFencedCodeBlocks();

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(
            <<<'MARKDOWN'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam tristique ipsum vitae tellus pharetra tempor. Suspendisse sollicitudin ex eu sapien fermentum, nec lacinia enim euismod. Fusce ac neque porta velit egestas mollis in ac justo. Morbi porttitor leo risus, ut accumsan tellus fringilla eu. Pellentesque ex ipsum, aliquet vel orci ut, tincidunt elementum erat. Sed nec leo massa. Curabitur massa felis, mattis ac nulla vulputate, mollis maximus dui. Pellentesque ac varius neque, eget placerat velit. Morbi eget ipsum odio. Sed eget arcu metus. Morbi pharetra sodales elit sed vehicula. Praesent efficitur velit ac fringilla accumsan. Vivamus scelerisque sem suscipit, pulvinar elit et, malesuada nunc.

```php
<?php

declare(strict_types=1);

final class Foo
{
    private Bar $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
```

Etiam consequat imperdiet magna in congue. Nunc laoreet tortor eget nunc bibendum, vitae finibus eros convallis. Nam molestie risus eu sem accumsan convallis. Ut fermentum nunc vel hendrerit tempor. Vestibulum ex est, iaculis ut urna et, fermentum scelerisque neque. Integer condimentum enim dolor, a lacinia eros ullamcorper sed. Donec dictum lacus nisl, ac imperdiet sapien porta sit amet. Donec congue felis hendrerit, pretium nibh sit amet, sodales diam. Morbi eros urna, pretium ut ante venenatis, ullamcorper dictum eros. Suspendisse quis molestie erat. Nullam ut lorem tristique, commodo purus ac, viverra justo. Nunc sed ullamcorper tellus, id ornare metus. Aenean pharetra, odio sed facilisis efficitur, enim nulla fringilla risus, consequat hendrerit eros sem eu ipsum. In mattis fermentum ultricies. Pellentesque ac orci in urna fringilla rhoncus non sed felis. Aliquam nec diam sit amet enim porta tempor.

```
final class Command
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function execute()
    {
        if (null !== $this->logger) {
            $this->logger->info();
        }
    }
}
```

Praesent varius est vel elit aliquam, eu consequat nisi feugiat. Nulla facilisi. Fusce posuere tincidunt leo ac facilisis. Vestibulum ex mi, faucibus at feugiat vel, volutpat non risus. Quisque ipsum mauris, rhoncus non purus non, luctus auctor leo. Sed facilisis lobortis leo, eu consequat mi tempus nec. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam convallis at odio non sodales. In non tempor sapien. Maecenas est arcu, venenatis sed interdum vitae, accumsan eu magna. Fusce volutpat odio a nisi porta consequat. Duis ipsum ex, laoreet eget condimentum sed, hendrerit mattis massa. Nunc nec viverra nisl. Fusce at orci a ligula ornare vestibulum. Vestibulum luctus tortor vitae mauris rhoncus malesuada. Praesent molestie auctor viverra.

MARKDOWN,
        );

        self::assertEquals($expected, $processed);
    }
}
