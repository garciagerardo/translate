const USERNAME_LENGTH = 5
const PASSWORD_LENGTH = 6

function validate(form){
	fail = ""
	fail += validateUsername(form.username.value)
	fail += validatePassword(form.password.value)

	if (fail == "") return true
	else { alert(fail); return false }
}

function validateUsername(field){
	if (field == "") return "No Username was entered.\n"
	else if (field.length < USERNAME_LENGTH)
		return "Usernames must be at least " + USERNAME_LENGTH + " characters.\n"
	else if (/[^a-zA-Z0-9_-]/.test(field))
		return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"
	return ""
}

function validatePassword(field){
	if (field == "") return "No Password was entered.\n"
	else if (field.length < PASSWORD_LENGTH)
		return "Passwords must be at least " + PASSWORD_LENGTH + " characters.\n"
	else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) ||!/[0-9]/.test(field))
		return "Passwords require one each of a-z, A-Z and 0-9.\n"
	return ""
}