# Notes

# Bug issues

- ERR_REDIRECT_01
If you are including partials before header() was called. It will break header function.
Because I'm naming a function that's render a header components, default header function was replaced.
