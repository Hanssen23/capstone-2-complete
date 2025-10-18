<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            Log::warning('Validation failed', [
                'url' => $request->url(),
                'errors' => $e->errors(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (NotFoundHttpException $e) {
            Log::warning('Page not found', [
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }
            
            abort(404);
            
        } catch (MethodNotAllowedHttpException $e) {
            Log::warning('Method not allowed', [
                'url' => $request->url(),
                'method' => $request->method(),
                'allowed_methods' => $e->getHeaders()['Allow'] ?? [],
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed'
                ], 405);
            }
            
            abort(405);
            
        } catch (QueryException $e) {
            Log::error('Database query error', [
                'url' => $request->url(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred'
                ], 500);
            }
            
            return back()->with('error', 'A database error occurred. Please try again.');
            
        } catch (\Exception $e) {
            Log::error('Unhandled exception', [
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred'
                ], 500);
            }
            
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
