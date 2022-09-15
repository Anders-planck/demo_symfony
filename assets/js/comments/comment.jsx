import React, {useCallback, useEffect, useRef, useState} from "react";
import { createRoot } from 'react-dom/client';
import {useFecth, usePaginatorFetch} from "./hooks";
import Field from "../components/formField";
import {unmountComponentAtNode} from "react-dom";

const dateFormat = {
    dateStyle: "medium",
    timeStyle: "short"
}

const VIEW = "VIEW"
const EDIT = "EDIT"

function Comments({post,user}) {
    const {items:comments,setItems: setComments,load,loading,hasMore,totalItems} = usePaginatorFetch("/api/comments?post="+post);
    const addComment = useCallback((comment)=>{
        setComments(comments => [comment, ...comments])
    },[])

    const deleteComment = useCallback((comment)=>{
        setComments(comments => comments.filter(c => c !==comment))
    },[])

    const updateComment = useCallback((newComment,oldComment)=>{
        setComments(comments => comments.map(c => c === oldComment ? newComment : c ))
    },[])

    useEffect(()=>{
        load().then()
    },[])
    return (
        <>
            <h1 className={"fw-bold"}> {totalItems} Commentaires</h1>

            {user && <CommentForm post={post} onComment={addComment}/>}

            {loading &&
                <div className="alert alert-primary" role="alert">
                    <li  className="list-group-item">
                        <div className="" aria-hidden="true">
                            <div className="card-body">
                                <h5 className="card-title placeholder-glow">
                                    <span className="placeholder col-6"></span>
                                </h5>
                                <p className="card-text placeholder-glow">
                                    <span className="placeholder col-7"></span>
                                    <span className="placeholder col-4"></span>
                                    <span className="placeholder col-4"></span>
                                    <span className="placeholder col-6"></span>
                                    <span className="placeholder col-8"></span>
                                </p>
                            </div>
                        </div>
                    </li>
                </div>
            }

            <ul className="list-group ">
                {comments.map(c => <Comment
                    key={c.id}
                    comment={c}
                    onDelete={deleteComment}
                    canEdit={c.author.id === user}
                    onUpdate={updateComment}
                    />
                )}
            </ul>

            {hasMore &&   <button className={"btn btn-primary my-2"} disabled={!hasMore} onClick={load}> see more</button>}
        </>
    )
}

const CommentForm = React.memo(({post=null,onComment,comment=null,onCancel=null})=>{
    const ref = useRef(null);
    const onSuccess = useCallback((comment)=>{
        onComment(comment)
        ref.current.value = ''
    },[ref,onComment])
    const method = comment ? "PUT" : "POST"
    const url = comment ? comment["@id"] : "/api/comments"
    const {load,loading,errors,clearError} = useFecth(url,method,onSuccess)

    const onSubmit = useCallback((e) => {
        e.preventDefault()
        let data = {
            content: ref.current.value
        }

        if(post) data.post =  "/api/posts/"+post
        load(data).then()
    },[load,ref])

    useEffect(()=>{
        if(comment && comment.content && ref.current)
            ref.current.value = comment.content
    },[comment,ref])
    return (
        <div className={"mb-5 mt-3"}>
            <form onSubmit={onSubmit}>
                <Field
                    ref={ref}
                    name={"content"}
                    required
                    minLength={5}
                    onchange={clearError.bind(this,"content")}
                    help={"Les commentaires bizzares seront supprimées"}
                    error={errors["content"]}
                >
                    Votre message ....
                </Field>
                <div className={"form-group"}>
                    <button className={"btn btn-primary mt-2 mx-2"} disabled={loading}>{comment ? "Edit" : "Envoyer"}</button>

                </div>
            </form>
            {onCancel && <button className={"btn btn-secondary"} onClick={onCancel}>Annuler</button>}
        </div>

    )
})

const Comment = React.memo(({comment=null,onDelete=null,onUpdate=null,canEdit=false})=>{

    const [state,setState] = useState(VIEW)
   // const toggleEdit = useCallback(() => setState(state === VIEW ? EDIT : VIEW),[])
    const toggleEdit = () => setState(state === VIEW ? EDIT : VIEW)
    const onDeleteComment = useCallback(()=>{
        onDelete(comment);
    },[comment])

    const onUpdateComment = useCallback((newComment)=>{
        onUpdate(newComment,comment);
        setState(VIEW)
    },[comment])

    const {loading:loadingDelete,load: callDelete} = useFecth(comment["@id"],"DELETE",onDeleteComment)
    let date = new Date()
    if(comment)
        date = new Date(comment.published_at)

    return (
        <>
        {comment && (
            <li  className={`list-group-item my-2 list-group-item d-flex justify-content-between align-items-start `}>
                    <div className="" aria-hidden="true">
                        <div className="card-body">
                            <h6 className="card-title placeholder-glow">
                                <span className="fw-bold">{comment.author.email} {comment.author.id}</span>
                            </h6>
                            {state === VIEW ?
                                (<p className="card-text placeholder-glow p-2">
                                    <span className="text-break">{comment.content}</span>
                                </p>
                                ):(
                                    <CommentForm comment={comment} onComment={onUpdateComment} onCancel={toggleEdit}/>
                                )
                            }
                        </div>
                    </div>
                {state !== EDIT &&
                    (
                        <div className={"d-flex flex-column"}>
                    <span className="badge bg-primary rounded-pill mb-2">
                        Le {date.toLocaleString(undefined,dateFormat)}
                    </span>
                            {canEdit  &&
                                <>
                                    <button
                                        className={"btn btn-danger"}
                                        onClick={callDelete.bind(this,null)}
                                        disabled={loadingDelete}
                                    >
                                        supprimer
                                    </button>
                                    <button
                                        className={"btn btn-secondary"}
                                        onClick={toggleEdit}
                                    >
                                        edit
                                    </button>
                                </>}
                        </div>
                    )
                }
                </li>
        )}
        </>
    )
})

class CommentsElement extends HTMLElement{

    constructor() {
        super();
        this.observer = null
    }

    connectedCallback(){
        const post  =  parseInt(this.dataset.post,10)
        const user = parseInt(this.dataset.user,10) || null
        if(this.observer === null){
            this.observer = new IntersectionObserver((entries,observer) => {
                entries.forEach(entry =>{
                    if(entry.isIntersecting && entry.target === this){
                        observer.disconnect()
                        createRoot(this).render(<Comments post={post} user={user}/>)
                    }
                })
            })
        }

        this.observer.observe(this)
    }

    disconnectedCallback(){
        if(this.observer!==null)
            this.observer.disconnect()
        unmountComponentAtNode(this)
    }
}

customElements.define("post-comments",CommentsElement);