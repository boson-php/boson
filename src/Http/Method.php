<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Method\HttpMethod;
use Boson\Http\Method\MethodStringableImpl;
use Boson\Shared\ValueObject\BackedEnumLikeImpl;

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Get} instead
 */
const METHOD_GET = new HttpMethod('GET', true, true);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Head} instead
 */
const METHOD_HEAD = new HttpMethod('HEAD', true, true);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Options} instead
 */
const METHOD_OPTIONS = new HttpMethod('OPTIONS', true, true);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Trace} instead
 */
const METHOD_TRACE = new HttpMethod('TRACE', true, true);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Put} instead
 */
const METHOD_PUT = new HttpMethod('PUT', true, false);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Delete} instead
 */
const METHOD_DELETE = new HttpMethod('DELETE', true, false);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Post} instead
 */
const METHOD_POST = new HttpMethod('POST', false, false);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Patch} instead
 */
const METHOD_PATCH = new HttpMethod('PATCH', false, false);

/**
 * @internal this constant cannot be autoloaded, please use {@see Method::Connect} instead
 */
const METHOD_CONNECT = new HttpMethod('CONNECT', false, false);

/**
 * The Request Method (VERB)
 *
 * This type also contains constants for a number of common HTTP methods such
 * as `GET`, `POST`, etc.
 *
 * Currently includes 8 variants representing the 8 methods defined in
 * {@link https://tools.ietf.org/html/rfc7231#section-4.1 RFC 7230}, plus
 * `PATCH`, and an Extension variant for all extensions.
 */
final readonly class Method implements MethodInterface
{
    /** @use BackedEnumLikeImpl<MethodInterface, non-empty-uppercase-string> */
    use BackedEnumLikeImpl;
    use MethodStringableImpl;

    /**
     * The `GET` method requests transfer of a current selected representation
     * for the target resource. `GET` is the primary mechanism of information
     * retrieval and the focus of almost all performance optimizations.
     * Hence, when people speak of retrieving some identifiable information
     * via HTTP, they are generally referring to making a `GET` request.
     *
     * It is tempting to think of resource identifiers as remote file system
     * pathnames and of representations as being a copy of the contents of
     * such files. In fact, that is how many resources are implemented (see
     * Section 9.1 for related security considerations). However, there are
     * no such limitations in practice. The HTTP interface for a resource
     * is just as likely to be implemented as a tree of content objects, a
     * programmatic view on various database records, or a gateway to other
     * information systems. Even when the URI mapping mechanism is tied to
     * a file system, an origin server might be configured to execute the
     * files with the request as input and send the output as the
     * representation rather than transfer the files directly. Regardless,
     * only the origin server needs to know how each of its resource
     * identifiers corresponds to an implementation and how each
     * implementation manages to select and send a current representation of
     * the target resource in a response to `GET`.
     *
     * A client can alter the semantics of `GET` to be a "range request",
     * requesting transfer of only some part(s) of the selected
     * representation, by sending a Range header field in the request
     * ({@link https://datatracker.ietf.org/doc/html/rfc7233 RFC 7233}).
     *
     * A payload within a `GET` request message has no defined semantics;
     * sending a payload body on a `GET` request might cause some existing
     * implementations to reject the request.
     *
     * The response to a `GET` request is cacheable; a cache MAY use it to
     * satisfy subsequent `GET` and `HEAD` requests unless otherwise indicated
     * by the `Cache-Control` header field
     * ({@link https://datatracker.ietf.org/doc/html/rfc7234#section-5.2 Section
     * 5.2 of RFC 7234}).
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.1
     */
    public const HttpMethod Get = METHOD_GET;

    /**
     * The `HEAD` method is identical to `GET` except that the server MUST NOT
     * send a message body in the response (i.e., the response terminates at
     * the end of the header section). The server SHOULD send the same
     * header fields in response to a `HEAD` request as it would have sent if
     * the request had been a `GET`, except that the payload header fields
     * (Section 3.3) MAY be omitted. This method can be used for obtaining
     * metadata about the selected representation without transferring the
     * representation data and is often used for testing hypertext links for
     * validity, accessibility, and recent modification.
     *
     * A payload within a `HEAD` request message has no defined semantics;
     * sending a payload body on a `HEAD` request might cause some existing
     * implementations to reject the request.
     *
     * The response to a `HEAD` request is cacheable; a cache MAY use it to
     * satisfy subsequent `HEAD` requests unless otherwise indicated by the
     * `Cache-Control` header field (Section 5.2 of [RFC7234]). A `HEAD`
     * response might also have an effect on previously cached responses to
     * `GET`; see Section 4.3.5 of [RFC7234].
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.2
     */
    public const HttpMethod Head = METHOD_HEAD;

    /**
     * The `POST` method requests that the target resource process the
     * representation enclosed in the request according to the resource's
     * own specific semantics.
     *
     * For example, `POST` is used for the following functions (among others):
     *
     * - Providing a block of data, such as the fields entered into an HTML
     *  form, to a data-handling process;
     *
     * - Posting a message to a bulletin board, newsgroup, mailing list,
     *  blog, or similar group of articles;
     *
     * - Creating a new resource that has yet to be identified by the
     *  origin server; and
     *
     * - Appending data to a resource's existing representation(s).
     *
     * An origin server indicates response semantics by choosing an
     * appropriate status code depending on the result of processing the
     * `POST` request; almost all of the status codes defined by this
     * specification might be received in a response to `POST` (the exceptions
     * being 206 (`Partial Content`), 304 (`Not Modified`), and 416 (`Range Not
     * Satisfiable`)).
     *
     * If one or more resources has been created on the origin server as a
     * result of successfully processing a `POST` request, the origin server
     * SHOULD send a 201 (`Created`) response containing a `Location` header
     * field that provides an identifier for the primary resource created
     * (Section 7.1.2) and a representation that describes the status of the
     * request while referring to the new resource(s).
     *
     * Responses to `POST` requests are only cacheable when they include
     * explicit freshness information (see Section 4.2.1 of [RFC7234]).
     * However, `POST` caching is not widely implemented. For cases where an
     * origin server wishes the client to be able to cache the result of a
     * `POST` in a way that can be reused by a later `GET`, the origin server
     * MAY send a 200 (OK) response containing the result and a
     * `Content-Location` header field that has the same value as the `POST`'s
     * effective request URI (Section 3.1.4.2).
     *
     * If the result of processing a `POST` would be equivalent to a
     * representation of an existing resource, an origin server MAY redirect
     * the user agent to that resource by sending a 303 (`See Other`) response
     * with the existing resource's identifier in the Location field. This
     * has the benefits of providing the user agent a resource identifier
     * and transferring the representation via a method more amenable to
     * shared caching, though at the cost of an extra request if the user
     * agent does not already have the representation cached.
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.3
     */
    public const HttpMethod Post = METHOD_POST;

    /**
     * The `PUT` method requests that the state of the target resource be
     * created or replaced with the state defined by the representation
     * enclosed in the request message payload. A successful `PUT` of a given
     * representation would suggest that a subsequent `GET` on that same
     * target resource will result in an equivalent representation being
     * sent in a 200 (OK) response. However, there is no guarantee that
     * such a state change will be observable, since the target resource
     * might be acted upon by other user agents in parallel, or might be
     * subject to dynamic processing by the origin server, before any
     * subsequent `GET` is received. A successful response only implies that
     * the user agent's intent was achieved at the time of its processing by
     * the origin server.
     *
     * If the target resource does not have a current representation and the
     * `PUT` successfully creates one, then the origin server MUST inform the
     * user agent by sending a 201 (`Created`) response. If the target
     * resource does have a current representation and that representation
     * is successfully modified in accordance with the state of the enclosed
     * representation, then the origin server MUST send either a 200 (`OK`) or
     * a 204 (`No Content`) response to indicate successful completion of the
     * request.
     *
     * An origin server SHOULD ignore unrecognized header fields received in
     * a `PUT` request (i.e., do not save them as part of the resource state).
     *
     * An origin server SHOULD verify that the `PUT` representation is
     * consistent with any constraints the server has for the target
     * resource that cannot or will not be changed by the `PUT`. This is
     * particularly important when the origin server uses internal
     * configuration information related to the URI in order to set the
     * values for representation metadata on `GET` responses. When a `PUT`
     * representation is inconsistent with the target resource, the origin
     * server SHOULD either make them consistent, by transforming the
     * representation or changing the resource configuration, or respond
     * with an appropriate error message containing sufficient information
     * to explain why the representation is unsuitable. The 409 (`Conflict`)
     * or 415 (`Unsupported Media Type`) status codes are suggested, with the
     * latter being specific to constraints on `Content-Type` values.
     *
     * For example, if the target resource is configured to always have a
     * `Content-Type` of `text/html` and the representation being `PUT` has a
     * `Content-Type` of `image/jpeg`, the origin server ought to do one of:
     *
     * a. reconfigure the target resource to reflect the new media type;
     *
     * b. transform the `PUT` representation to a format consistent with that
     *  of the resource before saving it as the new resource state; or,
     *
     * c. reject the request with a 415 (`Unsupported Media Type`) response
     *  indicating that the target resource is limited to "text/html",
     *  perhaps including a link to a different resource that would be a
     *  suitable target for the new representation.
     *
     * HTTP does not define exactly how a `PUT` method affects the state of an
     * origin server beyond what can be expressed by the intent of the user
     * agent request and the semantics of the origin server response. It
     * does not define what a resource might be, in any sense of that word,
     * beyond the interface provided via HTTP. It does not define how
     * resource state is "stored", nor how such storage might change as a
     * result of a change in resource state, nor how the origin server
     * translates resource state into representations. Generally speaking,
     * all implementation details behind the resource interface are
     * intentionally hidden by the server.
     *
     * An origin server MUST NOT send a validator header field
     * (Section 7.2), such as an `ETag` or `Last-Modified` field, in a
     * successful response to `PUT` unless the request's representation data
     * was saved without any transformation applied to the body (i.e., the
     * resource's new representation data is identical to the representation
     * data received in the `PUT` request) and the validator field value
     * reflects the new representation. This requirement allows a user
     * agent to know when the representation body it has in memory remains
     * current as a result of the PUT, thus not in need of being retrieved
     * again from the origin server, and that the new validator(s) received
     * in the response can be used for future conditional requests in order
     * to prevent accidental overwrites (Section 5.2).
     *
     * The fundamental difference between the `POST` and `PUT` methods is
     * highlighted by the different intent for the enclosed representation.
     * The target resource in a `POST` request is intended to handle the
     * enclosed representation according to the resource's own semantics,
     * whereas the enclosed representation in a `PUT` request is defined as
     * replacing the state of the target resource. Hence, the intent of `PUT`
     * is idempotent and visible to intermediaries, even though the exact
     * effect is only known by the origin server.
     *
     * Proper interpretation of a `PUT` request presumes that the user agent
     * knows which target resource is desired. A service that selects a
     * proper URI on behalf of the client, after receiving a state-changing
     * request, SHOULD be implemented using the `POST` method rather than PUT.
     * If the origin server will not make the requested `PUT` state change to
     * the target resource and instead wishes to have it applied to a
     * different resource, such as when the resource has been moved to a
     * different URI, then the origin server MUST send an appropriate 3xx
     * (`Redirection`) response; the user agent MAY then make its own decision
     * regarding whether or not to redirect the request.
     *
     * A `PUT` request applied to the target resource can have side effects on
     * other resources. For example, an article might have a URI for
     * identifying "the current version" (a resource) that is separate from
     * the URIs identifying each particular version (different resources
     * that at one point shared the same state as the current version
     * resource). A successful `PUT` request on "the current version" URI
     * might therefore create a new version resource in addition to changing
     * the state of the target resource, and might also cause links to be
     * added between the related resources.
     *
     * An origin server that allows `PUT` on a given target resource MUST send
     * a 400 (`Bad Request`) response to a `PUT` request that contains a
     * Content-Range header field (Section 4.2 of [RFC7233]), since the
     * payload is likely to be partial content that has been mistakenly PUT
     * as a full representation. Partial content updates are possible by
     * targeting a separately identified resource with state that overlaps a
     * portion of the larger resource, or by using a different method that
     * has been specifically defined for partial updates (for example, the
     * `PATCH` method defined in [RFC5789]).
     *
     * Responses to the `PUT` method are not cacheable. If a successful `PUT`
     * request passes through a cache that has one or more stored responses
     * for the effective request URI, those stored responses will be
     * invalidated (see Section 4.4 of [RFC7234]).
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.4
     */
    public const HttpMethod Put = METHOD_PUT;

    /**
     * The `DELETE` method requests that the origin server remove the
     * association between the target resource and its current
     * functionality. In effect, this method is similar to the rm command
     * in UNIX: it expresses a deletion operation on the URI mapping of the
     * origin server rather than an expectation that the previously
     * associated information be deleted.
     *
     * If the target resource has one or more current representations, they
     * might or might not be destroyed by the origin server, and the
     * associated storage might or might not be reclaimed, depending
     * entirely on the nature of the resource and its implementation by the
     * origin server (which are beyond the scope of this specification).
     * Likewise, other implementation aspects of a resource might need to be
     * deactivated or archived as a result of a `DELETE`, such as database or
     * gateway connections. In general, it is assumed that the origin
     * server will only allow `DELETE` on resources for which it has a
     * prescribed mechanism for accomplishing the deletion.
     *
     * Relatively few resources allow the `DELETE` method -- its primary use
     * is for remote authoring environments, where the user has some
     * direction regarding its effect. For example, a resource that was
     * previously created using a `PUT` request, or identified via the
     * Location header field after a 201 (`Created`) response to a `POST`
     * request, might allow a corresponding `DELETE` request to undo those
     * actions. Similarly, custom user agent implementations that implement
     * an authoring function, such as revision control clients using HTTP
     * for remote operations, might use `DELETE` based on an assumption that
     * the server's URI space has been crafted to correspond to a version
     * repository.
     *
     * If a `DELETE` method is successfully applied, the origin server SHOULD
     * send a 202 (`Accepted`) status code if the action will likely succeed
     * but has not yet been enacted, a 204 (`No Content`) status code if the
     * action has been enacted and no further information is to be supplied,
     * or a 200 (`OK`) status code if the action has been enacted and the
     * response message includes a representation describing the status.
     *
     * A payload within a `DELETE` request message has no defined semantics;
     * sending a payload body on a `DELETE` request might cause some existing
     * implementations to reject the request.
     *
     * Responses to the `DELETE` method are not cacheable. If a `DELETE`
     * request passes through a cache that has one or more stored responses
     * for the effective request URI, those stored responses will be
     * invalidated (see Section 4.4 of [RFC7234]).
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.5
     */
    public const HttpMethod Delete = METHOD_DELETE;

    /**
     * The `CONNECT` method requests that the recipient establish a tunnel to
     * the destination origin server identified by the request-target and,
     * if successful, thereafter restrict its behavior to blind forwarding
     * of packets, in both directions, until the tunnel is closed. Tunnels
     * are commonly used to create an end-to-end virtual connection, through
     * one or more proxies, which can then be secured using TLS (Transport
     * Layer Security, [RFC5246]).
     *
     * `CONNECT` is intended only for use in requests to a proxy. An origin
     * server that receives a `CONNECT` request for itself MAY respond with a
     * 2xx (`Successful`) status code to indicate that a connection is
     * established. However, most origin servers do not implement `CONNECT`.
     *
     * A client sending a `CONNECT` request MUST send the authority form of
     * request-target (Section 5.3 of [RFC7230]); i.e., the request-target
     * consists of only the host name and port number of the tunnel
     * destination, separated by a colon. For example,
     *
     * ```
     * CONNECT server.example.com:80 HTTP/1.1
     * Host: server.example.com:80
     * ```
     *
     * The recipient proxy can establish a tunnel either by directly
     * connecting to the request-target or, if configured to use another
     * proxy, by forwarding the `CONNECT` request to the next inbound proxy.
     * Any 2xx (`Successful`) response indicates that the sender (and all
     * inbound proxies) will switch to tunnel mode immediately after the
     * blank line that concludes the successful response's header section;
     * data received after that blank line is from the server identified by
     * the request-target. Any response other than a successful response
     * indicates that the tunnel has not yet been formed and that the
     * connection remains governed by HTTP.
     *
     * A tunnel is closed when a tunnel intermediary detects that either
     * side has closed its connection: the intermediary MUST attempt to send
     * any outstanding data that came from the closed side to the other
     * side, close both connections, and then discard any remaining data
     * left undelivered.
     *
     * Proxy authentication might be used to establish the authority to
     * create a tunnel. For example,
     *
     * ```
     * CONNECT server.example.com:80 HTTP/1.1
     * Host: server.example.com:80
     * Proxy-Authorization: basic aGVsbG86d29ybGQ=
     * ```
     *
     * There are significant risks in establishing a tunnel to arbitrary
     * servers, particularly when the destination is a well-known or
     * reserved TCP port that is not intended for Web traffic. For example,
     * a `CONNECT` to a request-target of "example.com:25" would suggest that
     * the proxy connect to the reserved port for SMTP traffic; if allowed,
     * that could trick the proxy into relaying spam email. Proxies that
     * support `CONNECT` SHOULD restrict its use to a limited set of known
     * ports or a configurable whitelist of safe request targets.
     *
     * A server MUST NOT send any `Transfer-Encoding` or `Content-Length` header
     * fields in a 2xx (`Successful`) response to `CONNECT`. A client MUST
     * ignore any `Content-Length` or `Transfer-Encoding` header fields received
     * in a successful response to `CONNECT`.
     *
     * A payload within a `CONNECT` request message has no defined semantics;
     * sending a payload body on a CONNECT request might cause some existing
     * implementations to reject the request.
     *
     * Responses to the `CONNECT` method are not cacheable.
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.6
     */
    public const HttpMethod Connect = METHOD_CONNECT;

    /**
     * The `OPTIONS` method requests information about the communication
     * options available for the target resource, at either the origin
     * server or an intervening intermediary. This method allows a client
     * to determine the options and/or requirements associated with a
     * resource, or the capabilities of a server, without implying a
     * resource action.
     *
     * An `OPTIONS` request with an asterisk ("*") as the request-target
     * (Section 5.3 of [RFC7230]) applies to the server in general rather
     * than to a specific resource. Since a server's communication options
     * typically depend on the resource, the "*" request is only useful as a
     * "ping" or "no-op" type of method; it does nothing beyond allowing the
     * client to test the capabilities of the server. For example, this can
     * be used to test a proxy for HTTP/1.1 conformance (or lack thereof).
     *
     * If the request-target is not an asterisk, the OPTIONS request applies
     * to the options that are available when communicating with the target
     * resource.
     *
     * A server generating a successful response to OPTIONS SHOULD send any
     * header fields that might indicate optional features implemented by
     * the server and applicable to the target resource (e.g., `Allow`),
     * including potential extensions not defined by this specification.
     * The response payload, if any, might also describe the communication
     * options in a machine or human-readable representation. A standard
     * format for such a representation is not defined by this
     * specification, but might be defined by future extensions to HTTP. A
     * server MUST generate a Content-Length field with a value of "0" if no
     * payload body is to be sent in the response.
     *
     * A client MAY send a `Max-Forwards` header field in an `OPTIONS` request
     * to target a specific recipient in the request chain (see
     * Section 5.1.2). A proxy MUST NOT generate a Max-Forwards header
     * field while forwarding a request unless that request was received
     * with a Max-Forwards field.
     *
     * A client that generates an `OPTIONS` request containing a payload body
     * MUST send a valid `Content-Type` header field describing the
     * representation media type. Although this specification does not
     * define any use for such a payload, future extensions to HTTP might
     * use the `OPTIONS` body to make more detailed queries about the target
     * resource.
     *
     * Responses to the `OPTIONS` method are not cacheable.
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.7
     */
    public const HttpMethod Options = METHOD_OPTIONS;

    /**
     * The `TRACE` method requests a remote, application-level loop-back of
     * the request message. The final recipient of the request SHOULD
     * reflect the message received, excluding some fields described below,
     * back to the client as the message body of a 200 (`OK`) response with a
     * `Content-Type` of "message/http" (Section 8.3.1 of [RFC7230]). The
     * final recipient is either the origin server or the first server to
     * receive a `Max-Forwards` value of zero (0) in the request
     * (Section 5.1.2).
     *
     * A client MUST NOT generate header fields in a `TRACE` request
     * containing sensitive data that might be disclosed by the response.
     * For example, it would be foolish for a user agent to send stored user
     * credentials [RFC7235] or cookies [RFC6265] in a `TRACE` request. The
     * final recipient of the request SHOULD exclude any request header
     * fields that are likely to contain sensitive data when that recipient
     * generates the response body.
     *
     * `TRACE` allows the client to see what is being received at the other
     * end of the request chain and use that data for testing or diagnostic
     * information. The value of the Via header field (Section 5.7.1 of
     * [RFC7230]) is of particular interest, since it acts as a trace of the
     * request chain. Use of the Max-Forwards header field allows the
     * client to limit the length of the request chain, which is useful for
     * testing a chain of proxies forwarding messages in an infinite loop.
     *
     * A client MUST NOT send a message body in a `TRACE` request.
     *
     * Responses to the `TRACE` method are not cacheable.
     *
     * R. Fielding, R., & Reschke, J.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.8
     */
    public const HttpMethod Trace = METHOD_TRACE;

    /**
     * The `PATCH` method requests that a set of changes described in the
     * request entity be applied to the resource identified by the Request-
     * URI. The set of changes is represented in a format called a "patch
     * document" identified by a media type. If the Request-URI does not
     * point to an existing resource, the server MAY create a new resource,
     * depending on the patch document type (whether it can logically modify
     * a null resource) and permissions, etc.
     *
     * The difference between the `PUT` and `PATCH` requests is reflected in the
     * way the server processes the enclosed entity to modify the resource
     * identified by the Request-URI. In a `PUT` request, the enclosed entity
     * is considered to be a modified version of the resource stored on the
     * origin server, and the client is requesting that the stored version
     * be replaced. With `PATCH`, however, the enclosed entity contains a set
     * of instructions describing how a resource currently residing on the
     * origin server should be modified to produce a new version. The `PATCH`
     * method affects the resource identified by the Request-URI, and it
     * also MAY have side effects on other resources; i.e., new resources
     * may be created, or existing ones modified, by the application of a
     * `PATCH`.
     *
     * `PATCH` is neither safe nor idempotent as defined by [RFC2616], Section
     * 9.1.
     *
     * A `PATCH` request can be issued in such a way as to be idempotent,
     * which also helps prevent bad outcomes from collisions between two
     * `PATCH` requests on the same resource in a similar time frame.
     * Collisions from multiple `PATCH` requests may be more dangerous than
     * `PUT` collisions because some patch formats need to operate from a
     * known base-point or else they will corrupt the resource. Clients
     * using this kind of patch application SHOULD use a conditional request
     * such that the request will fail if the resource has been updated
     * since the client last accessed the resource. For example, the client
     * can use a strong `ETag` [RFC2616] in an If-Match header on the `PATCH`
     * request.
     *
     * There are also cases where patch formats do not need to operate from
     * a known base-point (e.g., appending text lines to log files, or non-
     * colliding rows to database tables), in which case the same care in
     * client requests is not needed.
     *
     * The server MUST apply the entire set of changes atomically and never
     * provide (e.g., in response to a `GET` during this operation) a
     * partially modified representation. If the entire patch document
     * cannot be successfully applied, then the server MUST NOT apply any of
     * the changes. The determination of what constitutes a successful
     * `PATCH` can vary depending on the patch document and the type of
     * resource(s) being modified. For example, the common 'diff' utility
     * can generate a patch document that applies to multiple files in a
     * directory hierarchy. The atomicity requirement holds for all
     * directly affected files. See "Error Handling", Section 2.2, for
     * details on status codes and possible error conditions.
     *
     * If the request passes through a cache and the Request-URI identifies
     * one or more currently cached entities, those entries SHOULD be
     * treated as stale. A response to this method is only cacheable if it
     * contains explicit freshness information (such as an Expires header or
     * "`Cache-Control: max-age`" directive) as well as the `Content-Location`
     * header matching the Request-URI, indicating that the `PATCH` response
     * body is a resource representation. A cached `PATCH` response can only
     * be used to respond to subsequent `GET` and `HEAD` requests; it MUST NOT
     * be used to respond to other methods (in particular, PATCH).
     *
     * Note that entity-headers contained in the request apply only to the
     * contained patch document and MUST NOT be applied to the resource
     * being modified. Thus, a `Content-Language` header could be present on
     * the request, but it would only mean (for whatever that's worth) that
     * the patch document had a language. Servers SHOULD NOT store such
     * headers except as trace information, and SHOULD NOT use such header
     * values the same way they might be used on `PUT` requests. Therefore,
     * this document does not specify a way to modify a document's `Content-
     * Type` or `Content-Language` value through headers, though a mechanism
     * could well be designed to achieve this goal through a patch document.
     *
     * There is no guarantee that a resource can be modified with `PATCH`.
     * Further, it is expected that different patch document formats will be
     * appropriate for different types of resources and that no single
     * format will be appropriate for all types of resources. Therefore,
     * there is no single default patch document format that implementations
     * are required to support. Servers MUST ensure that a received patch
     * document is appropriate for the type of resource identified by the
     * Request-URI.
     *
     * Clients need to choose when to use `PATCH` rather than `PUT`. For
     * example, if the patch document size is larger than the size of the
     * new resource data that would be used in a `PUT`, then it might make
     * sense to use `PUT` instead of PATCH. A comparison to `POST` is even more
     * difficult, because `POST` is used in widely varying ways and can
     * encompass `PUT` and `PATCH`-like operations if the server chooses. If
     * the operation does not modify the resource identified by the Request-
     * URI in a predictable way, `POST` should be considered instead of `PATCH`
     * or `PUT`.
     *
     * Dusseault, L., & Snell, J.
     *
     * @link https://tools.ietf.org/html/rfc5789#section-2
     */
    public const HttpMethod Patch = METHOD_PATCH;

    /**
     * @var non-empty-string
     *
     * @phpstan-var non-empty-uppercase-string
     */
    public string $name;

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        public bool $isIdempotent = false,
        public bool $isSafe = false,
    ) {
        $this->name = \strtoupper($name);
    }

    protected static function caseKeyFor(object $case): string
    {
        assert($case instanceof MethodInterface);

        return $case->name;
    }
}
