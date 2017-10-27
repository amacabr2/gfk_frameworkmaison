<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 12/10/17
 * Time: 14:14
 */

namespace Tests\Framework\Middleware;


use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;

class CsrfMiddlewareTest extends TestCase {

    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    /**
     * @var DelegateInterface
     */
    private $delegate;

    /**
     * @var array
     */
    private $session;

    public function setUp() {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
        $this->delegate = $this->getMockBuilder(DelegateInterface::class)
            ->setMethods(['process'])
            ->getMock();
    }

    public function testLetGetRequestPass() {
        $this->delegate->expects($this->once())
            ->method('process')
            ->willReturn(new Response());
        $request = (new ServerRequest('GET', '/demo'));
        $this->middleware->process($request, $this->delegate);
    }

    public function testBlockPostRequestWithoutCsrf() {
        $this->delegate->expects($this->never())->method('process');
        $request = (new ServerRequest('POST', '/demo'));
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testBlockPostRequestWithInvalidCsrf() {
        $this->delegate->expects($this->never())->method('process');
        $request = (new ServerRequest('POST', '/demo'));
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'azerty']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testLetPostWithTokenPass() {
        $this->delegate->expects($this->once())
            ->method('process')
            ->willReturn(new Response());
        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $this->delegate);
    }

    public function testLetPostWithTokenPassOnce() {
        $this->delegate->expects($this->once())
            ->method('process')
            ->willReturn(new Response());
        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $this->delegate);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $this->delegate);
    }

    public function testLimitTokenNumber() {
        for ($i = 0; $i < 100; ++$i) {
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }

}