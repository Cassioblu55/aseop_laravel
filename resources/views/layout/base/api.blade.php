
Route::get('/base_names', function (){
return \App\Base_name::all();
});

Route::get('/base_names/{base_name}', function(\App\Base_name $base_name){
return $base_name;
});
