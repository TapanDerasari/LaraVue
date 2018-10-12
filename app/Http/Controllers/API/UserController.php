<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
	public function __construct() {
		$this->middleware( 'auth:api' );

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$this->authorize('isAdmin');
		return User::latest()->paginate( 10 );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$this->validate( $request, [
			'name'     => 'required |string|max:191 ',
			'email'    => 'required|string|max:191|unique:users',
			'type'     => 'required',
			'password' => 'required|string|min:6',
		] );

		return User::create( [
			'name'     => $request->name,
			'email'    => $request->email,
			'type'     => $request->type,
			'bio'      => $request->bio,
			'photo'    => $request->photo,
			'password' => Hash::make( $request->password ),
		] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		//
	}

	public function profile() {
		return auth( 'api' )->user();
	}


	public function updateProfile( Request $request ) {
		$user = auth( 'api' )->user();


		$this->validate( $request, [
			'name'     => 'required |string|max:191 ',
			'email'    => 'required|string|max:191|unique:users,email,' . $user->id,
			'type'     => 'required',
			'password' => 'sometimes|min:6',
		] );


		$currentphoto = $user->photo;
		if ( $request->photo != $currentphoto ) {
			$name = time() . '.' . explode( '/', explode( ':', substr( $request->photo, 0, strpos( $request->photo, ';' ) ) )[1] )[1];
//			print_r($name);exit();
			\Image::make( $request->photo )->save( public_path( 'img/profile/' ) . $name );

			$request->merge( [ 'photo' => $name ] );

			$oldPhoto = public_path( 'img\profile\\' ) . $currentphoto;
//			print_r( $oldPhoto );
//			exit();
			if ( file_exists( $oldPhoto ) ) {
				@unlink( $oldPhoto );
			}
		}

		if ( ! empty( $request->password ) ) {
			$request->merge( [ 'password' => Hash::make( $request['password'] ) ] );
		}

		$user->update( $request->all() );

		return [ 'message' => 'Profile Updated.' ];
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, $id ) {
		$user = User::findOrfail( $id );
		$this->validate( $request, [
			'name'     => 'required |string|max:191 ',
			'email'    => 'required|string|max:191|unique:users,email,' . $user->id,
			'type'     => 'required',
			'password' => 'sometimes|min:6',
		] );
		if ( ! empty( $request->password ) ) {
			$request->merge( [ 'password' => Hash::make( $request['password'] ) ] );
		}
		$user->update( $request->all() );

		return [ 'message' => 'User information has been updated successfully.' ];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id ) {
		$this->authorize('isAdmin');
		$user = User::findOrfail( $id );
		$user->delete();

		return [ 'message' => 'User has been deleted successfully.' ];
	}
}
