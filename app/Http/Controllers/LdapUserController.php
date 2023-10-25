<?php

namespace App\Http\Controllers;

use App\Ldap\User as LdapUser;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use LdapRecord\Exceptions\ConstraintViolationException;
use LdapRecord\Exceptions\InsufficientAccessException;
use LdapRecord\LdapRecordException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LdapUserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ChangePassword(Request $request)
    {
        $user = auth()->user();
        $form = $request->all();

        Validator::make($form, [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
        ])->after(function ($validator) use ($user) {
            if (!Hash::check(request()->get('current_password'), $user->password)) {
                $validator->errors()->add('current_password', __('La contraseña proporcionada no coincide con su contraseña actual.'));
            }
        })->validateWithBag('updatePassword');

        try {
            $UserLdap = LdapUser::findBy('samaccountname', auth()->user()->username);
            $UserLdap->unicodepwd = $request->password;
            $UserLdap->save();
            User::find(auth()->user()->id)
                ->update([
                    'password' => Hash::make(request()->get('password')),
                ]);

            return response()->json('password changed successfully', 200);
        } catch (InsufficientAccessException $ex) {
            return response()->json([
                'message_resp' => 'the user does not have sufficient permissions to change the password',
                'error' => $ex,
            ], 422);
        } catch (ConstraintViolationException $ex) {
            $error = $ex->getDetailedError();

            return response()->json([
                'message_resp' => 'The password does not comply with the domain`s security policy',
                'error' => $ex,
                'errorCode' => $error->getErrorCode(),
                'errorMessage' => $error->getErrorMessage(),
                'diagnosticMessage' => $error->getDiagnosticMessage(),
            ], 422);
        } catch (LdapRecordException $ex) {
            $error = $ex->getDetailedError();

            return response()->json([
                'message_resp' => 'Failed changing password',
                'error' => $ex,
                'errorCode' => $error->getErrorCode(),
                'errorMessage' => $error->getErrorMessage(),
                'diagnosticMessage' => strpos($error->getDiagnosticMessage(), '52D'),
            ], 422);
        } catch (Exception|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        try {
            $local_user = User::where('email', '=', $request->email)->first();

            $UserLdap = LdapUser::findBy('samaccountname', $local_user->username);
            $UserLdap->unicodepwd = $request->password;
            $UserLdap->save();
            $local_user->update([
                'password' => Hash::make(request()->get('password')),
            ]);

            return response()->json('password changed successfully', 200);
        } catch (InsufficientAccessException $ex) {
            return response()->json([
                'message_resp' => 'the user does not have sufficient permissions to change the password',
                'error' => $ex,
            ], 422);
        } catch (ConstraintViolationException $ex) {
            $error = $ex->getDetailedError();

            return response()->json([
                'message_resp' => 'The password does not comply with the domain`s security policy',
                'error' => $ex,
                'errorCode' => $error->getErrorCode(),
                'errorMessage' => $error->getErrorMessage(),
                'diagnosticMessage' => $error->getDiagnosticMessage(),
            ], 422);
        } catch (LdapRecordException $ex) {
            return response()->json($ex->getMessage(), 422);
        } catch (Exception|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return response()->json($e, 500);
        }

    }
}
