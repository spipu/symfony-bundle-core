<?php
namespace Spipu\CoreBundle\Tests\Unit\Fixture;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Fixture\FixtureInterface;
use Spipu\CoreBundle\Fixture\ListFixture;
use Symfony\Component\Console\Output\OutputInterface;

class ListFixtureTest extends TestCase
{
    /**
     * @var string
     */
    private string $outputText = '';

    public function testLoad()
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $outputMock->expects($this->any())->method('writeln')->will(
            $this->returnCallback(
                function ($text, $options = 0) {
                    $this->outputText .= $text . ',';
                }
            )
        );

        $fixtureA = $this->createMock(FixtureInterface::class);
        $fixtureA->expects($this->any())->method('getCode')->willReturn('test_a');
        $fixtureA->expects($this->any())->method('getOrder')->willReturn(10);
        $fixtureA->expects($this->never())->method('remove');
        $fixtureA->expects($this->once())->method('load')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('load_a');
                }
            )
        );


        $fixtureB = $this->createMock(FixtureInterface::class);
        $fixtureB->expects($this->any())->method('getCode')->willReturn('test_b');
        $fixtureB->expects($this->any())->method('getOrder')->willReturn(30);
        $fixtureB->expects($this->never())->method('remove');
        $fixtureB->expects($this->once())->method('load')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('load_b');
                }
            )
        );

        $fixtureC = $this->createMock(FixtureInterface::class);
        $fixtureC->expects($this->any())->method('getCode')->willReturn('test_c');
        $fixtureC->expects($this->any())->method('getOrder')->willReturn(20);
        $fixtureC->expects($this->never())->method('remove');
        $fixtureC->expects($this->once())->method('load')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('load_c');
                }
            )
        );

        $this->outputText = '';

        $service = new ListFixture([$fixtureA, $fixtureB, $fixtureC]);

        $this->assertSame($fixtureA, $service->get('test_a'));
        $this->assertSame($fixtureB, $service->get('test_b'));
        $this->assertSame($fixtureC, $service->get('test_c'));

        $service->load($outputMock);

        $this->assertSame("load_a,load_c,load_b,", $this->outputText);

        $this->expectException(\Exception::class);
        $service->get('wrong_code');
    }

    public function testRemove()
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $outputMock->expects($this->any())->method('writeln')->will(
            $this->returnCallback(
                function ($text, $options = 0) {
                    $this->outputText .= $text . ',';
                }
            )
        );

        $fixtureA = $this->createMock(FixtureInterface::class);
        $fixtureA->expects($this->any())->method('getCode')->willReturn('test_a');
        $fixtureA->expects($this->any())->method('getOrder')->willReturn(10);
        $fixtureA->expects($this->never())->method('load');
        $fixtureA->expects($this->once())->method('remove')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('remove_a');
                }
            )
        );

        $fixtureB = $this->createMock(FixtureInterface::class);
        $fixtureB->expects($this->any())->method('getCode')->willReturn('test_b');
        $fixtureB->expects($this->any())->method('getOrder')->willReturn(30);
        $fixtureB->expects($this->never())->method('load');
        $fixtureB->expects($this->once())->method('remove')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('remove_b');
                }
            )
        );

        $fixtureC = $this->createMock(FixtureInterface::class);
        $fixtureC->expects($this->any())->method('getCode')->willReturn('test_c');
        $fixtureC->expects($this->any())->method('getOrder')->willReturn(20);
        $fixtureC->expects($this->never())->method('load');
        $fixtureC->expects($this->once())->method('remove')->will(
            $this->returnCallback(
                function (OutputInterface $output) {
                    $output->writeln('remove_c');
                }
            )
        );

        $this->outputText = '';

        $service = new ListFixture([$fixtureA, $fixtureB, $fixtureC]);
        $service->remove($outputMock);

        $this->assertSame("remove_a,remove_c,remove_b,", $this->outputText);
    }
}
