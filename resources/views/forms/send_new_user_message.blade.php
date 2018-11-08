<form action="/message/send" method="POST">
	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif
	<div>
		Номер пользователя: <input type="" name="user_id" />
	</div>
	<div>
		Сообщение пользователя: <input type="" name="message" />
	</div>
	{{ csrf_field() }}
	<button type="submit">Отправить</button>
</form>