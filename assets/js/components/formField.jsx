import React from "react";

const className = (...arr) => arr.filter(Boolean).join(" ")
const Field = React.forwardRef(( {children,help,name,error,onchange,required,minLength}, ref) =>{
   if(error) help = error
   return (
    <div className={className("form-floating",error && "is-invalid")}>
                    <textarea className={className("form-control",error && "is-invalid")} ref={ref}   name={name}  placeholder="Leave a comment here" id={name}
                              style={{height:"200px"}} onChange={onchange} required={required} minLength={minLength}></textarea>
        <label htmlFor={name}>{children}</label>
        {help && <div className={className("alert  mt-2",error ? "alert-danger" : "alert-info ")}> {help}</div>}
    </div>
   )
})


export default Field;