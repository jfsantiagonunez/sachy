module TransactionsHelper
  def updateCategory(params)
    category_id = params["transaction"]["category_id"]
    puts("Category #{category_id}")
    @transaction.category_id=category_id
    @transaction.save
    return true
  end
end
