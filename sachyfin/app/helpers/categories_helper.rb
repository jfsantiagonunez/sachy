module CategoriesHelper
  
  def findRules(category_id)
    return Rule.where(category_id: category_id)
  end
  
  def findCategories(grupo_id)
    return Category.where(grupo_id: grupo_id)
  end
  
  def createRule(name,category_id)
    return Rule.where(name: name, category_id:category_id).first_or_create()
  end
  
  def applyRule(rule)
    like_rule = "%#{rule.name}%"
    transactions = Transaction.where('description like ?', like_rule)
    transactions.each do |transaction|
      transaction.category_id = rule.category_id
      transaction.save
    end
  end
  
  def untagCategory(category_id)
    default =1
    transactions = Transaction.where(category_id: category_id)
    transactions.each do |transaction|
      transaction.category_id = default
      transaction.save
    end
  end
  
  def getCategoriesForSelect()
    return Category.all.pluck(:name,:id)
  end
end
